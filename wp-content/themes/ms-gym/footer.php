<?php 
$additionalOptions = get_field('additional_options', 'options');
$logo2 = $additionalOptions['logo_2'];
$location = $additionalOptions['location'];
$additionalText = $additionalOptions['additional_text'];
?>

<div id="footer">
    <div class="footer-inner flex-display-align">
        <div class="footer-logo flex-50">
            <?php echo $logo2 ? '<a href="'. home_url('/') .'" title="Home"><img class="footer-logo-img img-responsive" src="'. $logo2['url'] .'" title="'. $logo2['title'] .'" alt="'. $logo2['alt'] .'"></a>' : ''; ?>
        </div>
        <div class="footer-info flex-50">
            <?php echo $location ? '<div class="location">'. $location .'</div>' : ''; ?>
            <?php echo $additionalText ? '<div class="additional-info">'. $additionalText .'</div>' : ''; ?>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>

</html>
