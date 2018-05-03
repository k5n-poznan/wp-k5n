<div class="wrap">
    <h2><?php _e('Eksport', 'wp-k5n'); ?></h2>
    <form id="export-filters" method="post" action="<?php echo plugins_url('wp-k5n/export.php'); ?>">
        <table>
            <tr valign="top">
                <th scope="row">
                    <label for="export-file-type"><?php _e('Export To', 'wp-k5n'); ?>:</label>
                </th>

                <td>
                    <select id="export-file-type" name="export-file-type">
                        <option value="0"><?php _e('Proszę wybrać.', 'wp-k5n'); ?></option>
                        <option value="excel">Excel</option>
                        <option value="xml">XML</option>
                        <option value="csv">CSV</option>
                        <option value="tsv">TSV</option>
                    </select>
                    <p class="description"><?php _e('Wybierz typ pliku wyjściowego.', 'wp-k5n'); ?></p>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <a href="admin.php?page=wp-k5n-subscribers" class="button"><?php _e('Powrót', 'wp-k5n'); ?></a>
                    <input type="submit" class="button-primary" name="wps_export_subscribe"
                           value="<?php _e('Export', 'wp-k5n'); ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>