<?php
$page = $pagination_page_number - 1;
$next = $pagination_page_number + 1;
$lastpage = ceil($total_number_of_items / $number_of_items_per_page);
$lpm1 = $lastpage - 1;

// Number of adjacent pages should be shown on each side
$adjacents = 3;

/*
  Now we apply our rules and draw the pagination object.
  We're actually saving the code to a variable in case we want to draw it more than once.
 */
$pagination = "<img id='undo-button' title='Undo' src='images/undo.png' onclick='undoAction();'/>";
if ($lastpage > 1) {
    $pagination .= "<div id=\"pagination_wrapper_div\">";
    //previous button
    if ($pagination_page_number > 1) {
        $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($page)\">&#9668;</a>";
    } else {
        $pagination .= "<a class=\"button gray_out_button\">&#9668;</a>";
    }

    //pages	
    if ($lastpage < 7 + ($adjacents * 2)) { //not enough pages to bother breaking it up
        for ($counter = 1; $counter <= $lastpage; $counter++) {
            if ($counter == $pagination_page_number) {
                $pagination .= "<a class=\"button gray_out_button\">$counter</a>";
            } else {
                $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($counter)\">$counter</a>";
            }
        }
    } elseif ($lastpage > 5 + ($adjacents * 2)) { //enough pages to hide some
        //close to beginning; only hide later pages
        if ($pagination_page_number < 1 + ($adjacents * 2)) {
            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                if ($counter == $pagination_page_number) {
                    $pagination .= "<a class=\"button gray_out_button\">$counter</a>";
                } else {
                    $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($counter)\">$counter</a>";
                }
            }
            $pagination .= "...";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($lpm1)\">$lpm1</a>";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($lastpage)\">$lastpage</a>";
        } elseif ($lastpage - ($adjacents * 2) > $pagination_page_number && $pagination_page_number > ($adjacents * 2)) { //in middle; hide some front and some back
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page(1)\">1</a>";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page(2)\">2</a>";
            $pagination .= "...";
            for ($counter = $pagination_page_number - $adjacents; $counter <= $pagination_page_number + $adjacents; $counter++) {
                if ($counter == $pagination_page_number) {
                    $pagination .= "<a class=\"button gray_out_button\">$counter</a>";
                } else {
                    $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($counter)\">$counter</a>";
                }
            }
            $pagination .= "...";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($lpm1)\">$lpm1</a>";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($lastpage)\">$lastpage</a>";
        }
        //close to end; only hide early pages
        else {
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page(1)\">1</a>";
            $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page(2)\">2</a>";
            $pagination .= "...";
            for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                if ($counter == $pagination_page_number) {
                    $pagination .= "<a class=\"button gray_out_button\">$counter</a>";
                } else {
                    $pagination .= "<a class=\"button page_button\" onclick=\"go_to_page($counter)\">$counter</a>";
                }
            }
        }
    }

    //next button
    if ($pagination_page_number < $counter - 1)
        $pagination .= "<a class=\"button page_button next_and_previous_buttons\" onclick=\"go_to_page($next)\">&#9658;</a>";
    else
        $pagination .= "<a class=\"button gray_out_button next_and_previous_buttons\">&#9658;</a>";
    $pagination .= "</div>\n";
}
?>

