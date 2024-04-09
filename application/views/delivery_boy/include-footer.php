<footer class="main-footer text-right">
    <?php $settings = get_settings('system_settings', true);
    if (isset($settings['copyright_details']) && !empty($settings['copyright_details'])) {
    ?>
        <strong> <?= (isset($settings['copyright_details']) && !empty($settings['copyright_details'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['copyright_details'])) : " " ?> </strong>
    <?php } else { ?>
        <strong>Copyright &copy; <?= date('Y') ?> - <?= date('Y') + 1 ?> <a target="_blank" href="<?= base_url('admin/home') ?>"><?php echo $settings['app_name']; ?></a>, All Right Reserved <a target="_blank" href="https://www.wrteam.in/">WRTeam</a>.</strong>
    <?php } ?>
    
</footer>