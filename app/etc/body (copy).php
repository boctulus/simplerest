<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<style type="text/css">

    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    /* Prevent WebKit and Windows mobile changing default text sizes */
  
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    /* Remove spacing between tables in Outlook 2007 and up */

    img { -ms-interpolation-mode: bicubic; }
    /* Allow smoother rendering of resized image in Internet Explorer */

    /* RESET STYLES */
    img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    table { border-collapse: collapse !important; }
    body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* ---------------- */
    /* HIGHLIGHT STYLES */
    /* ---------------- */

    /* FIRST TABLE: Highlights the entire table row when hovering over the first column */
    /* These styles targets Gmail and Yahoo! email clients */
    .row1:hover, .row1:hover + td, .row1:hover + td + td, .row1:hover + td + td + td,
    .row2:hover, .row2:hover + td, .row2:hover + td + td, .row2:hover + td + td + td,
    .row3:hover, .row3:hover + td, .row3:hover + td + td, .row3:hover + td + td + td,
    .row4:hover, .row4:hover + td, .row4:hover + td + td, .row4:hover + td + td + td,
    .row5:hover, .row5:hover + td, .row5:hover + td + td, .row5:hover + td + td + td,
    .row6:hover, .row6:hover + td, .row6:hover + td + td, .row6:hover + td + td + td,
    /* Following code highlights individual table cells */
    .row1 + td:hover, .row1 + td + td:hover, .row1 + td + td + td:hover,
    .row2 + td:hover, .row2 + td + td:hover, .row2 + td + td + td:hover,
    .row3 + td:hover, .row3 + td + td:hover, .row3 + td + td + td:hover,
    .row4 + td:hover, .row4 + td + td:hover, .row4 + td + td + td:hover,
    .row5 + td:hover, .row5 + td + td:hover, .row5 + td + td + td:hover,
    .row6 + td:hover, .row6 + td + td:hover, .row6 + td + td + td:hover {
        background-color: #fb7d7a !important;
    }

    /* SECOND TABLE: Highlights the entire table row when hovering over the first column */
    .row7:hover, .row7:hover + td, .row7:hover + td + td, .row7:hover + td + td + td,
    .row8:hover, .row8:hover + td, .row8:hover + td + td, .row8:hover + td + td + td,
    .row9:hover, .row9:hover + td, .row9:hover + td + td, .row9:hover + td + td + td,
    .row10:hover, .row10:hover + td, .row10:hover + td + td, .row10:hover + td + td + td,
    .row11:hover, .row11:hover + td, .row11:hover + td + td, .row11:hover + td + td + td,
    .row12:hover, .row12:hover + td, .row12:hover + td + td, .row12:hover + td + td + td,
    /* Following code highlights individual table cells */
    .row7 + td:hover, .row7 + td + td:hover, .row7 + td + td + td:hover,
    .row8 + td:hover, .row8 + td + td:hover, .row8 + td + td + td:hover,
    .row9 + td:hover, .row9 + td + td:hover, .row9 + td + td + td:hover,
    .row10 + td:hover, .row10 + td + td:hover, .row10 + td + td + td:hover,
    .row11 + td:hover, .row11 + td + td:hover, .row11 td + td + td:hover,
    .row12 + td:hover, .row12 + td + td:hover, .row12 + td + td + td:hover {
        background-color: #54da86 !important;
    }

    /* SECOND TABLE: Highlights the entire table row up to the left-most hovered column */
    /* Currently only works in Gmail webmail */
    .column1:hover ~ td, .column2:hover ~ td, .column3:hover ~td {
        background-color: #54da86 !important;
    }

    /* THIRD TABLE: Highlights the entire table row when hovering over the first column */
    /* These styles targets Gmail and Yahoo! email clients */
    .row13:hover + td, .row13:hover + td + td + td,
    .row14:hover + td + td + td,
    .row15:hover + td, .row15:hover + td + td,
    .row16:hover + td, .row16:hover + td + td + td,
    .row17:hover + td, .row17:hover + td + td + td,
    .row18:hover + td, .row18:hover + td + td, .row18:hover + td + td + td,
    /* Following code highlights individual table cells */
    .row13 + td:hover, .row13 + td + td:hover, .row13 + td + td + td:hover,
    .row14 + td:hover, .row14 + td + td:hover, .row14 + td + td + td:hover,
    .row15 + td:hover, .row15 + td + td:hover, .row15 + td + td + td:hover,
    .row16 + td:hover, .row16 + td + td:hover, .row16 + td + td + td:hover,
    .row17 + td:hover, .row17 + td + td:hover, .row17 + td + td + td:hover,
    .row18 + td:hover, .row18 + td + td:hover, .row18 + td + td + td:hover {
        background-color: #7d7afb !important;
    }

    /* Specific styles for Outlook.com */
    /* For Outlook.com, the class must be followed by an HTML element; cannot be class:hover or id:hover */
    .outlookRow1 td:hover,
    .outlookRow2 td:hover,
    .outlookRow3 td:hover,
    .outlookRow4 td:hover,
    .outlookRow5 td:hover,
    .outlookRow6 td:hover {
        background-color: #fb7d7a !important;
    }
    .outlookRow7 td:hover,
    .outlookRow8 td:hover,
    .outlookRow9 td:hover,
    .outlookRow10 td:hover,
    .outlookRow11 td:hover,
    .outlookRow12 td:hover {
        background-color: #54da86 !important;
    }
    .outlookRow13 td:hover,
    .outlookRow14 td:hover,
    .outlookRow15 td:hover,
    /*.outlookRow16 td:hover,*/
    .outlookRow17 td:hover,
    .outlookRow18 td:hover {
        background-color: #7d7afb !important;
    }


    /* 
    Generic Styling, for Desktops/Laptops 
    */
    table { 
    width: 100%; 
    border-collapse: collapse; 
    }
    /* Zebra striping */
    tr:nth-of-type(odd) { 
    background: #eee; 
    }
    th { 
    background: #333; 
    color: white; 
    font-weight: bold; 
    }
    td, th { 
    padding: 6px; 
    border: 1px solid #ccc; 
    text-align: left; 
    }

    /* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

	/* Force table to not be like tables anymore */
	table, thead, tbody, th, td, tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	tr { border: 1px solid #ccc; }
	
	td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
	}
	
	td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
	}
</style>
</head>

    <body bgcolor="#ffffff" style="margin: 0 !important; padding: 0 !important; background-color: #ffffff;">
        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="600">


            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: auto;" class="email-container">
                <?php
                    if (isset($image_header)){
                        echo "<tr>
                            <td style=\"padding: 20px 0; text-align: center\">
                                <img src=\"{$image_header['src']}\" width=\"{$image_header['width']}\" height=\"{$image_header['height']}\" border=\"0\" style=\"height: auto;  font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555;\">
                            </td>
                        </tr>";
                    }
                ?>

                <!-- Email Header : BEGIN -->
                
                <!-- Email Header : END -->

                <!-- Hero Image, Flush : BEGIN -->
                <!-- <tr>
                    <td style="background-color: #ffffff;" class="darkmode-bg">
                        <img src="https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png" width="600" height="" alt="alt_text" border="0" style="width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;" class="g-img">
                    </td>
                </tr> -->
                <!-- Hero Image, Flush : END -->
            </table>

            <!-- FIRST TABLE -->
            <tr>
                <td align="center" valign="top" width="100%" bgcolor="#ffffff" style="background-color: #ffffff;">
                    <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="598">
                        <tr>
                            <td>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="left" valign="top" bgcolor="#ffffff" style="background-color: #ffffff; margin: 0; padding: 0 0 40px 0; width: 100%;">
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">


                                                <tr>
                                                    <td align="left" valign="middle" width="100%" colspan="4" style="color: #000000; font-weight: 600; font-size: 16px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 40px 0 0 0;">Promotional Emails:</td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="middle" width="100%" colspan="4" style="color: #000000; font-weight: 400; font-size: 14px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 0;"><span style="font-weight: 600;">Date Sent</span>: Monday, February 1, 10:00am</td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="middle" width="100%" colspan="4" style="color: #000000; font-weight: 400; font-size: 14px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 0;"><span style="font-weight: 600;">List Size</span>: 240,234</td>
                                                </tr>
                                    


                                                <!-- INFO -->
                                                <?php
                                                    foreach ($cols as $ix => $col){
                                                        ?>
                                                            <td align="center" valign="middle" width="<?= $withs[$ix] ?>%" style="background-color: #df78ef; border: 1px solid #d05ce3; color: #ffffff; font-weight: 600; font-size: 14px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 10px 0;"><?= $col ?></td>
                                                        <?php
                                                    }
                                                ?>

                                                <!-- DATA -->
                                                <?php

                                                    foreach ($rows as $ix => $row){
                                                        $ii = $ix +1;

                                                        echo "<tr class=\"outlookRow{$ii}\">";                                                        
                                                        foreach ($row as $ij => $cell){
                                                            if ($ij === 0){
                                                                echo "<td class=\"row{$ii}\" align=\"center\" valign=\"middle\" width=\"16%\" style=\"border: 1px solid #d05ce3; color: #000000; font-weight: 600; font-size: 14px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 10px 0;\"><a href=\"#\" style=\"color: #000000; text-decoration: none;\">{$cell}</a></td>";
                                                            } else {
                                                                echo "<td align=\"center\" valign=\"middle\" width=\"28%\" style=\"border: 1px solid #828282; color: #000000; font-weight: 300; font-size: 14px; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 10px 0;\"><a href=\"#\" style=\"color: #000000; text-decoration: none;\">$cell</a></td>";
                                                            }
                                                        
                                                        }                                                        
                                                        echo '</tr>';
                                                    }
                                                ?>


                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- end first table -->


        </table>
    </body>
</html>