<?php
require_once('classes/database.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['search'])) {
        $searchterm = $_POST['search']; 
        $con = new database();

        try {
            $connection = $con->opencon();
            
            // Check if the connection is successful
            if ($connection) {
                // SQL query with JOIN
                $query = $connection->prepare("SELECT userss.user_id, userss.firstname, userss.lastname, userss.birthday, userss.sex, userss.user, userss.user_profile_picture, CONCAT(users_address.user_city,', ', users_address.user_province) AS address FROM userss INNER JOIN users_address ON userss.user_id = users_address.user_id WHERE userss.user LIKE ? OR userss.user_id LIKE ?");
                $query->execute(["%$searchterm%","%$searchterm%"]);
                $users = $query->fetchAll(PDO::FETCH_ASSOC);

                // Generate HTML for table rows
                $html = '';
                foreach ($users as $user) {
                    $html .= '<tr>';
                    $html .= '<td>' . $user['user_id'] . '</td>';
                    $html .= '<td><img src="' . htmlspecialchars($user['user_profile_picture']) . '" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;"></td>';
                    $html .= '<td>' . $user['firstname'] . '</td>';
                    $html .= '<td>' . $user['lastname'] . '</td>';
                    $html .= '<td>' . $user['birthday'] . '</td>';
                    $html .= '<td>' . $user['user'] . '</td>';
                    $html .= '<td>' . $user['user'] . '</td>';
                    $html .= '<td>' . $user['address'] . '</td>';
                    $html .= '<td>'; // Action column
                    $html .= '<form action="update.php" method="post" style="display: inline;">';
                    $html .= '<input type="hidden" name="id" value="' . $user['user_id'] . '">';
                    $html .= '<button type="submit" class="btn btn-primary btn-sm">Edit</button>';
                    $html .= '</form>';
                    $html .= '<form method="POST" style="display: inline;">';
                    $html .= '<input type="hidden" name="id" value="' . $user['user_id'] . '">';
                    $html .= '<input type="submit" name="delete" class="btn btn-danger btn-sm" value="Delete" onclick="return confirm(\'Are you sure you want to delete this user?\')">';
                    $html .= '</form>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                echo $html;
            } else {
                echo json_encode(['error' => 'Database connection failed.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'No search query provided.']);
    }
} 