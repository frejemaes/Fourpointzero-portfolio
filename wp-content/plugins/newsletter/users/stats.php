<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$all_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE);
$options_profile = get_option('newsletter_profile');

$module = NewsletterUsers::instance();
$controls = new NewsletterControls();
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Statistics', 'newsletter') ?></h2>

        <p>Reports values are usually computed only counting confirmed subscribers.</p>

    </div>

    <div id="tnp-body">

        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tab-overview">Overview</a></li>
                <li><a href="#tabs-preferences">Preferences</a></li>
                <li><a href="#tabs-countries">Countries</a></li>
                <li><a href="#tabs-referrers">Referrers</a></li>
                <li><a href="#tabs-sources">Sources</a></li>
                <li><a href="#tabs-gender">Gender</a></li>
                <li><a href="#tabs-time">By time</a></li>
            </ul>

            <div id="tab-overview">

                <table class="widefat" style="width: 300px;">
                    <thead><tr><th>Status</th><th>Total</th></thead>
                    <tr valign="top">
                        <td>Any</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirmed</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Not confirmed</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='S'"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Subscribed to feed by mail</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C' and feed=1"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Unsubscribed</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='U'"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Bounced</td>
                        <td>
                            <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='B'"); ?>
                        </td>
                    </tr>
                </table>

            </div>


            <div id="tabs-preferences">

                <div class="tab-preamble">
                    <p>
                        User count by preference.
                        <a href="http://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">Read more about preferences</a> and/or
                        configure them from te "Subscription Form" panel.
                    <p>
                </div>

                <table class="widefat" style="width: 300px;">
                    <thead>
                        <tr>
                            <th>Preference</th>
                            <th>Total</th>
                    </thead>
                    <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
                        <?php if (empty($options_profile['list_' . $i])) continue; ?>
                        <tr>
                            <td><?php echo '(' . $i . ') ' . $options_profile['list_' . $i]; ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $i . "=1 and status='C'"); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

            </div>


            <div id="tabs-countries">
                <div class="tab-preamble">
                    <?php if (!class_exists('NewsletterReports')) { ?>
                        <p><strong>The <a href="http://www.thenewsletterplugin.com/plugins/newsletter/reports-module" target="_blank">Reports Extension</a> is required
                                to compute the data for this chart.</strong></p>
                    <?php } ?>
                    <p>
                        This panel shows a World map with the number of subscriptions per country.
                    <p>
                </div>
                <?php
                if (class_exists('NewsletterReports')) {
                    $countries = $wpdb->get_results("select country, count(*) as total from " . NEWSLETTER_USERS_TABLE . " where status='C' and country<>'' group by country order by total");
                }
                ?>

                <?php if (empty($countries)) { ?>
                    <p>No data available, just wait some time to let the processor to work on your subscriber list. Thank you.</p>
                <?php } else { ?>
                    <p><div id="country-chart" style="width:400; height:300"></div></p>
                <?php } ?>

            </div>


            <div id="tabs-referrers">
                <div class="tab-preamble">
                    <p>The referrer is a special (hidden) fields collected during the subscription. For example the widget
                        adds the "widget" referrer to his generated form. With custom forms you can add
                        your own referrer using an hidden field named "nr".
                    </p>
                </div>
                <?php
                $list = $wpdb->get_results("select referrer, count(*) as total from " . NEWSLETTER_USERS_TABLE . " where status='C' group by referrer order by total desc");
                ?>
                <table class="widefat" style="width: 300px">
                    <thead>
                        <tr><th>Referrer</th><th>Total</th>
                    </thead>
                    <?php foreach ($list as $row) { ?>
                        <tr>
                            <td><?php echo empty($row->referrer) ? '[undefined]' : esc_html($row->referrer) ?></td>
                            <td><?php echo $row->total; ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <!--
    <canvas id="myChart" width="400" height="400"></canvas>
                <script>
                    var ctx = document.getElementById("myChart").getContext("2d");
                    var data = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: [65, 59, 80, 81, 56, 55, 40]
            },
            {
                label: "My Second dataset",
                fillColor: "rgba(151,187,205,0.5)",
                strokeColor: "rgba(151,187,205,0.8)",
                highlightFill: "rgba(151,187,205,0.75)",
                highlightStroke: "rgba(151,187,205,1)",
                data: [28, 48, 40, 19, 86, 27, 90]
            }
        ]
    };
    var myBarChart = new Chart(ctx).Bar(data);
    </script>
                -->
            </div>


            <div id="tabs-sources">

                <div class="tab-preamble">
                    <p>
                        URLs from which the subscription started. For example, if you use the widget on your blog sidebar
                        you can discover which page is converting more.
                    </p>
                </div>

                <?php
                $list = $wpdb->get_results("select http_referer, count(*) as total from " . NEWSLETTER_USERS_TABLE . " where status='C' group by http_referer order by count(*) desc limit 100");
                ?>
                <table class="widefat" style="width: 300px">
                    <thead><tr><th>URL</th><th>Total</th></thead>
                    <?php foreach ($list as $row) { ?>
                        <tr><td><?php echo $row->http_referer; ?></td><td><?php echo $row->total; ?></td></tr>
                    <?php } ?>
                </table>

            </div>


            <div id="tabs-gender">

                <?php
                $male_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where sex='m'");
                $female_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where sex='f'");
                $other_count = ($all_count - $male_count - $female_count)
                ?>
                <table class="widefat" style="width: 300px">
                    <thead><tr><th>Sex</th><th>Total</th></thead>
                    <tr><td>Male</td><td><?php echo $male_count; ?></td></tr>
                    <tr><td>Female</td><td><?php echo $female_count; ?></td></tr>
                    <tr><td>Not specified</td><td><?php echo $other_count; ?></td></tr>
                </table>

                <p><div id="sex-chart"></div></p>

            </div>


            <div id="tabs-time">

                <h4>Subscriptions by month (max 24 months)</h4>
                <?php
                $months = $wpdb->get_results("select count(*) as c, concat(year(created), '-', date_format(created, '%m')) as d from " . NEWSLETTER_USERS_TABLE . " where status='C' group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 24");
                ?>
                <div id="months-chart"></div>

                <table class="widefat" style="width: 300px">
                    <thead>
                        <tr valign="top">
                            <th>Date</th>
                            <th>Subscribers</th>
                        </tr>
                    </thead>
                    <?php foreach ($months as &$day) { ?>
                        <tr valign="top">
                            <td><?php echo $day->d; ?></td>
                            <td><?php echo $day->c; ?></td>
                        </tr>
                    <?php } ?>
                </table>

                <h4>Subscriptions by day (max 90 days)</h4>
                <?php
                $list = $wpdb->get_results("select count(*) as c, date(created) as d from " . NEWSLETTER_USERS_TABLE . " where status='C' group by date(created) order by d desc limit 90");
                ?>
                <table class="widefat" style="width: 300px">
                    <thead>
                        <tr valign="top">
                            <th>Date</th>
                            <th>Subscribers</th>
                        </tr>
                    </thead>
                    <?php foreach ($list as $day) { ?>
                        <tr valign="top">
                            <td><?php echo $day->d; ?></td>
                            <td><?php echo $day->c; ?></td>
                        </tr>
                    <?php } ?>
                </table>

            </div>

        </div>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1', {'packages': ['corechart', 'geochart']});

    google.setOnLoadCallback(drawChart);

    function drawChart() {


        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Gender');
        data.addColumn('number', 'Total');
        data.addRows([
            ['None', <?php echo $other_count; ?>],
            ['Female', <?php echo $female_count; ?>],
            ['Male', <?php echo $male_count; ?>]
        ]);

        var options = {'title': 'Gender',
            'width': 400,
            'height': 300};

        var chart = new google.visualization.PieChart(document.getElementById('sex-chart'));
        chart.draw(data, options);

        var months = new google.visualization.DataTable();
        months.addColumn('string', 'Month');
        months.addColumn('number', 'Subscribers');

<?php foreach ($months as $day) { ?>
            months.addRow(['<?php echo $day->d; ?>', <?php echo $day->c; ?>]);
<?php } ?>

        var options = {'title': 'By months', 'width': 700, 'height': 500};

        var chart = new google.visualization.BarChart(document.getElementById('months-chart'));
        chart.draw(months, options);

<?php if (!empty($countries)) { ?>
            var countries = new google.visualization.DataTable();
            countries.addColumn('string', 'Country');
            countries.addColumn('number', 'Total');
    <?php foreach ($countries as &$country) { ?>
                countries.addRow(['<?php echo $country->country; ?>', <?php echo $country->total; ?>]);
    <?php } ?>

            var options = {'title': 'Country', 'width': 700, 'height': 500};
            var chart = new google.visualization.GeoChart(document.getElementById('country-chart'));
            chart.draw(countries, options);
<?php } ?>
    }
</script>