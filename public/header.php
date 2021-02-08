<div class='header'>
    <img id='header-logo' src='images/logo.png'>
    <h2>Inventory Management System</h2>
    <div id='header-menu-items'>
        <div id='header-rooms-list-div'>
            <p>Room:</p>
                <?php require_once('../private/require/header-room-select-query.php'); ?>
        </div>
        <div id='header-cabinets-list-div'>
            <p>Cabinet:</p>
                <?php require_once('../private/require/header-cabinet-select-query.php'); ?>
        </div>
    </div>
    <span id='how-does-it-work-span' onclick='howDoesItWork()'>How does it work?</span>
    <span id='how-does-it-work-close-button' class='noselect' onclick='closeHowDoesItWork()'>X</span>
    <span id='how-does-it-work-next-button' class='noselect' onclick='howDoesItWorkNext()'>Next</span>
    <span id='how-does-it-work-snapshot-holder'></span>
    <span id='how-does-it-work-informative-text-holder'></span>
</div>



