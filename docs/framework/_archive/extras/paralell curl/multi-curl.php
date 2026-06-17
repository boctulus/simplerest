<?php
// (A) MULTI CURL
function mcurl ($list) {
  // (A1) MULTI-CURL INIT
  $mh = curl_multi_init();

  // (A2) CURL INIT
  $multi = []; $i = 0;
  foreach ($list as $url=>$l) {
    $multi[$i] = curl_init();
    curl_setopt($multi[$i], CURLOPT_URL, $url);
    curl_setopt($multi[$i], CURLOPT_RETURNTRANSFER, 1);
    if (isset($l['post'])) {
      curl_setopt($multi[$i], CURLOPT_POST, true);
      curl_setopt($multi[$i], CURLOPT_POSTFIELDS, $l['post']);
    }
    curl_multi_add_handle($mh, $multi[$i]);
    $i++;
  }

  // (A3) CURL EXEC
  do {
    // GET CURL EXEC STATUS
    $status = curl_multi_exec($mh, $active);

    // CREDITS: https://gist.github.com/Xeoncross/2362936
    // WHEN A CURL REQUEST IS COMPLETE - RUN CALLBACK
    if ($state = curl_multi_info_read($mh)) {
      $info = curl_getinfo($state['handle']);
      if (isset($list[$info['url']]['callback'])) {
        $callback = $list[$info['url']]['callback'];
        $callback(curl_multi_getcontent($state['handle']), $info);
      }
      curl_multi_remove_handle($mh, $state['handle']);
    }
    
    // SHORT PAUSE TO NOT FLOOD CPU
    usleep(1000);
  } while ($status == CURLM_CALL_MULTI_PERFORM || $active);
  
  // (A4) CASE CLOSED - ALL DONE
  curl_multi_close($mh);
}

// (B) CALLBACK FUNCTION - JUST OUTPUT RESULTS...
function output ($res) { echo $res; }

// (C) RUN!
mcurl([
  "http://localhost/dummy.php" => [
    "post" => ["KeyA" => "ValueA", "KeyB" => "ValueB"],
    "callback" => "output"
  ],
  "https://en.wikipedia.org/wiki/Portal:Gardening" => [
    "callback" => "output"
  ]
]);