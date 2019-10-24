<?php
$brightThemeColor = '#FFFFFF';
$lightThemeColor = '#EEEEEE';
$mediumThemeColor = '#CCCCCC';
$darkThemeColor = '#222222';
$alertThemeColor = '#FF0000';
$lightAlertThemeColor = '#FFCCCC';

$btc = $brightThemeColor;
$ltc = $lightThemeColor;
$mtc = $mediumThemeColor;
$dtc = $darkThemeColor;
$atc = $alertThemeColor;
$latc = $lightAlertThemeColor;
?>
    <style type="text/css">
    * {
        margin:0;
        padding:0;
        color:<?php echo $dtc;?>;
        box-sizing:border-box;
    }

    body {
        background-color:<?php echo $ltc;?>;
        font-family:'Montserrat';
    }
    
    main {
        text-align:center;
        margin:20px auto 0 auto;
    }
    
    .textBlock {
        white-space:pre-wrap;
    }
    
    .wrapperMain {
        min-width:30vw;
        max-width:80vw;
        text-align: center;
        display: inline-block;
        overflow-wrap:break-word;
    }
    
    .panel {
        background-color:<?php echo $btc;?>;
        margin-bottom:20px;
        padding:20px;
    }
    
    .panel > div {
        margin-bottom:20px;
    }
    
    .panel > div:nth-last-child(1) {
        margin-bottom:0;
    }

    h1, h2, h3, h4, h5, h6 {
        /*margin-bottom:20px;*/
        display:inline-block;
    }

    a {
        text-decoration:none;
    }

    header {
        padding:5px;
        border-bottom:2px solid <?php echo $dtc;?>;
        overflow:auto;
    }
    
    textarea {
        resize:vertical;
        height:100px;
    }
    
    input[type=text], input[type=password], textarea {
        padding:7px; 
        background-color:<?php echo $ltc;?>;
        color:<?php echo $dtc;?>;
        border:1px solid <?php echo $mtc;?>;
    }
    
    input[type=text]:focus, input[type=password]:focus, textarea:focus {
        background-color:<?php echo $btc;?>;
    }
    
    .form input[type=text], .form input[type=password] , .form textarea {
        /*inputs within a default form fill width - exception headerForm (no .form class)*/
        display:block;
        margin-bottom:20px;
        width:100%;
    }
    
    .form p.error {
        font-size:15px;
        color:<?php echo $atc;?>;
    }

    button {
        background-color:<?php echo $dtc;?>;
        border:1px solid <?php echo $mtc;?>;
        border-radius:3px;
        color:<?php echo $btc;?>;
        padding:5px 10px;
        text-align: center;
        text-decoration: none;
        display:inline-block;
        text-transform:uppercase;
        font-size:16px;
        cursor:pointer;
    }

    button:hover {
        background-color:<?php echo $ltc;?>;
        color:<?php echo $mtc;?>;
    }
    
    .buttonBar {
        text-align:right;
    }

    header {
        background:<?php echo $btc;?>;
    }

    header img{
        height:50px;
        border-radius:0 0 2px 2px;
    }

    header nav {
        vertical-align:top;
    }

    header ul {
        margin:20px 0 0 0;
        display:inline-block;
        vertical-align:top;
    }

    header ul>li {
        display: inline;
        padding: 0 5px 0 5px;
    }

    header ul>li>a:hover {
        color:<?php echo $mtc;?>;
    }

    header div {
        margin:15px 0 0 0;
        display:inline;
        float:right;
    }
    
    header form {
        margin:0;
        padding:0;
        display:inline-block;
    }
    
    .hidden {
        display:none !important;
    }
    
    .listItem {
        text-align:left;
    }
    
    .titleBar {
        display:flex;
    }
    .titleBar > h3{
        flex:1;
    }
    
    #responseLogIcon {background-image:url("img/icon.png");background-size:cover;position:fixed;bottom:20px;right:20px;min-height:50px;min-width:50px;cursor:pointer;z-index:1;}
    #responseLog {background-color:<?php echo $latc; ?>;position:fixed;display:inline-block;overflow-wrap:break-word;bottom:20px;right:20px;height:40vh;width:40vw;min-width:250px;border-radius:0 0 30px 0;overflow-y:auto;}
    
    @media(max-width:768px){
        header{
            text-align:center;
        }
        .wrapperMain{
            min-width:100vw;
        }
    }
    
    </style>
