<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 11:04 PM
 *
 * @author haronarama
 */

class Data_Table
{
    /**
     * Set table identifier
     * @var string
     */
    public $table_id;

    /**
     * Set table name
     * @var string
     */
    public $table_name;

    /**
     * Add table headers
     * @var string
     */
    public $print_th;

    /**
     * Add table data
     * @var string
     */
    public $print_td;

    /**
     * Add table styling
     * @var string
     */
    public $print_style;

    /**
     * The page hosting the data table
     * @var $page
     */
    public $page;

    /**
     * The database handler
     * @var PDO
     */
    public $db;

    /**
     * Constructor for the table
     *
     * @param $table_id
     * @param $table_name
     * @param $page
     */
    function __construct($table_id, $table_name, $page)
    {
        // Persist database connection to table from page loaded
        $this->db = $page->db;
        $this->page = $page;

        // Set up table
        $this->table_id = $table_id;
        $this->table_name = $table_name;
    }

    /**
     * Create a table from query
     *
     * @param $query // query to database
     * @param $columns // an array of columns to render
     * @param $column_names // names to be rendered in table headers
     * @param $table_id // table id that will be used to implement javaScripts for table
     * @param $add_class // add extra classes to table
     */
    function createTableFromQuery($query, $columns, $column_names, $allow_table_action = false, $table_id = -1, $add_class = "")
    {
        $id = "id=\"$table_id\"";
        $footer = "";

        // if no table id set default to $this->table_id
        if ($table_id == -1) {
            $table_id = $this->table_id;
            $id = "id=\"$table_id\"";
        }

        // Retrieve data from database
        $result = $this->page->db->query($query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        ?>
        <div class="table-responsive">
            <table <?php echo $id; ?> class="table table-striped table-bordered <?php echo $add_class; ?>"
                                      style="width:100%">
                <thead>
                <tr>
                    <?php
                    foreach ($column_names as $column_name) {
                        echo "<th>$column_name</th>";
                        $footer .= "<th>$column_name</th>"; // collect names to construct footer element
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($rows as $row) {
                    ?>
                    <tr>
                        <?php
                        $row_id = $row["id"];
                        foreach ($columns as $column) {
                            $data = $row["$column"]; // gather data
                            // render data as an entry in table
                            if ($column != "tenants_id") {
                                echo "<td>$data</td>";
                            } else {
                                $tenants = unserialize($data);
                                echo "<td>";
                                foreach ($tenants as $tenant) {
                                    $query = "SELECT * FROM `friends` WHERE id=$tenant";
                                    $result = $this->page->db->query($query);
                                    $row_t = $result->fetch(PDO::FETCH_ASSOC);
                                    $data = $row_t["first_name"] . " " . $row_t["last_name"];
                                    echo "<p>$data</p><br>";
                                }
                                echo "</td>";
                            }

                        }

                        if ($allow_table_action) {
                            $eid = $_GET['eid'] = $row_id;
                            echo "<td>
                                    <!-- Action dropdown button -->
                                    <div class=\"btn-group\">
                                      <button type=\"button\" class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                        Actions
                                      </button>
                                      <div class=\"dropdown-menu align-content-center\">
                                        <!-- Button trigger modal -->
                                        <a class=\"dropdown-item bg-success text-white\" href=\"?eid=$row_id\">Edit item</a>
                                        <div class=\"dropdown-divider\"></div>
                                        <a class=\"dropdown-item bg-danger text-white\" href=\"?id=$row_id\" onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete item</a>
                                      </div>
                                    </div>
                                  </td>";
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <?php
                echo $footer; // print footer element
                ?>
                </tfoot>
            </table>
        </div>
        <?php
    }

    /**
     * Edit table will create a pop up modal that will allow data entries to be edited
     *
     * @param $id
     * @param $columns // an array of columns to render
     * @param $column_names // names to be rendered in table headers
//     * @param $table_id // table id that will be used to implement javaScripts for table
     * @param $add_class // add extra classes to table
     */
//    function editDataTableEntry($id, $columns, $column_names, $add_class); implementation required

    /**
     * Delete friends from database and other information stored about them
     *
     * @param $id
     * @param $units_table // units
     * @param $products_table // checkout
     * @param $rent_payments_table // rent_payments
     * @param $deactivate // boolean
     */
    function deleteTenant($id, $deactivate=false, $units_table=false, $products_table=false, $rent_payments_table=false)
    {
        $select_q = "SELECT * FROM `friends` WHERE id=$id";
        $result = $this->page->db->query($select_q);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $tenant_id = $row['id'];
        $unit_id = $row['units_id'];

        if ($deactivate) {
            if ($units_table) {
                $select_units_q = "SELECT `tenants_id` FROM `units` WHERE `id`=$tenant_id";
                $result = $this->page->db->query($select_units_q);
                $row = $result->fetch(PDO::FETCH_ASSOC);

                $tenants = unserialize($row['tenants_id']);
                if (!empty($tenants)) {
                    for ($i = 0; $i < sizeof($tenants); $i++) {
                        if ($tenants[$i] == $tenant_id) {
                            unset($tenants[$i]);
                        }
                    }
                    $tenants = serialize($tenants);

                    $update_units_q = "UPDATE `units` SET `tenants_id` = f$tenantsriends WHERE `units`.id = $unit_id";

                    $this->page->db->query($update_units_q);
                }
            }

            if ($products_table) {
                $update_units_q = "UPDfriendsnants` SET `products_id` = '' WHERE id = $tenant_id";
                $this->page->db->query($update_units_q);

                $del_products_q = "DELETE FROM `checkout` WHERE `tenants_id`=$tenant_id";
                $this->page->db->query($del_products_q);
            }

            $update_tenants_query = "UPDfriendsnants` SET `status` = 'Inactive' WHfriendsnants`.`id` = $id";

            $this->page->db->query($update_tenants_query);

        } else {
            $query = "DELETE Ffriendsnants` WHERE `id`=$id";
            $this->page->db->query($query);
        }
    }
}