<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/17/18
 * Time: 5:28 PM
 *
 * @author haronarama
 */

class Data_Chart
{
    /**
     * Set chart identifier
     * @var string
     */
    public $chart_id;

    /**
     * Set chart name
     * @var string
     */
    public $chart_name;

    /**
     * Add chart headers
     * @var string
     */
    public $print_th;

    /**
     * Add chart data
     * @var string
     */
    public $print_td;

    /**
     * Add chart styling
     * @var string
     */
    public $print_style;

    /**
     * The page hosting the data chart
     * @var $page
     */
    public $page;

    /**
     * The database handler
     * @var PDO
     */
    public $db;

    /**
     * Constructor for the chart
     *
     * @param $chart_id
     * @param $chart_name
     * @param $page
     */
    function __construct($chart_id, $chart_name, $page)
    {
        // Persist database connection to chart from page loaded
        $this->db = $page->db;
        $this->page = $page;

        // Set up chart
        $this->chart_id = $chart_id;
        $this->chart_name = $chart_name;
    }

    /**
     * Create chart script from array of column names and chart data
     *
     * @param $data_points // an array of data_points to render
     * @param $column_names // names to be rendered in table headers
     * @param $chart_id // table id that will be used to implement javaScripts for chart
     */
    function createChart($data_points, $column_names, $chart_id)
    {
        ?>
        <!-- Graphs -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
        <script>
            var ctx = document.getElementById("myChart");
            var <?php echo $chart_id ?> = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        <?php
                            $i = 0;
                            foreach ($column_names as $column_name) {
                                echo "'$column_name'";
                                if ($i < sizeof($column_names) - 1) {
                                    echo ",";
                                }
                                $i++;
                            }
                        ?>
                    ],
                    datasets: [{
                        data: [
                            <?php
                            $i = 0;
                            foreach ($data_points as $data_point) {
                                echo "$data_point";
                                if ($i < sizeof($data_points) - 1) {
                                    echo ",";
                                }
                                $i++;
                            }
                            ?>
                        ],
                        lineTension: 0,
                        backgroundColor: 'transparent',
                        borderColor: '#007bff',
                        borderWidth: 4,
                        pointBackgroundColor: '#007bff'
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    }
                }
            });
        </script>
        <?php
    }
}