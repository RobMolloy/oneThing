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
    *       {margin:0;padding:0;color:<?php echo $dtc;?>;box-sizing:border-box;}
    
    body    {background-color:<?php echo $ltc;?>;font-family:'Montserrat';}
    main    {text-align:center;margin:20px auto 20px auto;}
    a       {text-decoration:none;}

    header  {display:flex;padding:5px;border-bottom:2px solid <?php echo $dtc;?>;overflow:auto;background:<?php echo $btc;?>;}
    header > * {display:flex;align-items:center;}
    header > *:nth-child(1) {flex:1;}
    header img {height:50px;border-radius:0 0 2px 2px;}
    
    input[type=text], input[type=password], textarea {
        padding:7px;background-color:<?php echo $ltc;?>;color:<?php echo $dtc;?>;
        border:1px solid <?php echo $mtc;?>;width:100%;
    }
    input[type=text]:focus, input[type=password]:focus, textarea:focus {background-color:<?php echo $btc;?>;}
    textarea {resize:vertical;height:100px;}

    button {
        background-color:<?php echo $dtc;?>;border:1px solid <?php echo $mtc;?>;border-radius:3px;
        color:<?php echo $btc;?>;padding:5px 10px;text-align: center;text-decoration: none;
        display:inline-block;text-transform:uppercase;font-size:16px;cursor:pointer;
    }
    
    button:hover {background-color:<?php echo $ltc;?>;color:<?php echo $mtc;?>;}
    
    .hidden {display:none !important;}
    .error {font-size:15px;color:<?php echo $atc;?>;}

    .singleColumn {display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;}
    .oneLineContents {display:flex;align-items:center;}
    .centerContents {display:flex;justify-content:center;align-items:center;}
    .centerContentsHorizontally {display:flex;justify-content:center;align-items:flex-start;}
    .centerContentsVertically {display:flex;align-items:center;}
    
    .wrapperMain {
        min-width:45%;max-width:80vw;text-align:center;overflow-wrap:break-word;
        /*.singleColumn*/
        display:inline-grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;
    }
    
    .panel {
        background-color:<?php echo $btc;?>;padding:20px;
        /*.singleColumn*/
        display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;
    }
    
    .textBlock {white-space:pre-wrap;}
    .buttonBar {text-align:right;}
    
    .displayMode {text-align:left;}
    .editMode {text-align:left;}
    
    .titleBar {display:flex;}
    .titleBar > * {display:flex;align-items:center;}
    .titleBar > *:nth-child(1) {flex:1;}
    
    #responseLogIcon {background-image:url("img/icon.png");background-size:cover;position:fixed;bottom:20px;right:20px;min-height:50px;min-width:50px;cursor:pointer;z-index:1;}
    #responseLog {background-color:<?php echo $latc; ?>;position:fixed;display:inline-block;overflow-wrap:break-word;bottom:20px;right:20px;height:40vh;width:40vw;min-width:250px;border-radius:0 0 30px 0;overflow-y:auto;}
    
    @media(max-width:768px){
        header{padding:0;flex-direction:column;justify-content:center;align-items:center;}
        header > * {display:flex;justify-content:center;width:100%}
        header button {width:100%;border:0;border-radius:0;}
        header img {max-width:100vw;height:auto;}
        
        .wrapperMain {min-width:100vw;max-width:100vw;}
    }
    
    </style>
