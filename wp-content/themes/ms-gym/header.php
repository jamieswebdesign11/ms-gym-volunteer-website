<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- VIEWPORT FOR MOBILE DEVICES -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TITLE & DESCRIPTION -->
    <title>
        <?php wp_title(); ?>
    </title>
    <!-- NO MORE THAN 75 CHAR.-->
    <?php wp_head(); ?>   
</head>

<body <?php body_class(); ?>>
<?php 
$additionalOptions = get_field('additional_options', 'options');
$logo1 = $additionalOptions['logo_1'];
?>
<div id="header">
    <div class="header-inner flex-display">
        <div class="logo flex-20">
            <div class="logo-inner">
                <?php echo $logo1 ? '<a href="'. home_url('/') .'" title="Home"><img class="logo-img img-responsive" src="'. $logo1['url'] .'" title="'. $logo1['title'] .'" alt="'. $logo1['alt'] .'"></a>' : ''; ?>
            </div>
        </div>
        <div class="navigation flex-80">
            <div id="main-nav" class="main-nav">
                <div class="mobile-nav"><i class="fas fa-bars"></i></div>
                <div id="menu-wrap" class="menu-wrap-container">
                    <?php wp_nav_menu(array('theme_location' => 'main_menu', 'container' => false, 'walker' => new wp_bootstrap_navwalker())); ?>
                </div>
            </div>
        </div>
    </div>
</div>
