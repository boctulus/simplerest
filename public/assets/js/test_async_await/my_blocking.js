
 /*
    non-blocking 

    https://stackoverflow.com/a/39914235/980631
*/
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// blocking
function count($till) {
    for (let x=0; x<$till; x++){

    }
}

// blocking
count(9999999991);

alert('Hi');