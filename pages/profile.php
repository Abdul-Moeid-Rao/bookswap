<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zip_code = trim($_POST['zip_code']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    
    // Handle file upload
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('profile_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $file_path)) {
                // Delete old profile picture if it exists
                if ($profile_pic && file_exists('../' . $profile_pic)) {
                    unlink('../' . $profile_pic);
                }
                $profile_pic = 'uploads/' . $file_name;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        }
    }
    
    if (empty($errors)) {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, address = ?, city = ?, state = ?, zip_code = ?, latitude = ?, longitude = ?, profile_pic = ? WHERE id = ?");
        $success = $stmt->execute([$full_name, $address, $city, $state, $zip_code, $latitude, $longitude, $profile_pic, $user_id]);
        
        if ($success) {
            $user = getUserById($user_id); // Refresh user data
            $success = true;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}

$page_title = "Your Profile";
include '../includes/header.php';
?>

<div class="profile-container">
    <h2>Your Profile</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert success">
            Profile updated successfully!
        </div>
    <?php endif; ?>
    
    <div class="profile-content">
        <div class="profile-sidebar">
            <div class="profile-picture">
                <img src="<?php echo !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : '../assets/images/profile-placeholder.png'; ?>" alt="Profile Picture">
            </div>
            <h3><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h3>
            <p>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <div class="profile-form">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="profile_pic">Profile Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                </div>
                
                <h3>Location Information</h3>
                <p>This helps us show you books that are nearby. Your exact address will not be shared with other users.</p>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="zip_code">Zip Code</label>
                    <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($user['latitude'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($user['longitude'] ?? ''); ?>">
                </div>
                
                <p><small>Don't know your coordinates? <a href="https://www.latlong.net/" target="_blank">Find them here</a>.</small></p>
                
                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script>
// Add geolocation functionality if the user hasn't set coordinates
document.addEventListener('DOMContentLoaded', function() {
    const latitudeField = document.getElementById('latitude');
    const longitudeField = document.getElementById('longitude');
    const addressField = document.getElementById('address');
    const cityField = document.getElementById('city');
    const stateField = document.getElementById('state');
    const zipField = document.getElementById('zip_code');
    
    // Only try to get location if coordinates aren't set
    if (!latitudeField.value || !longitudeField.value) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                latitudeField.value = position.coords.latitude.toFixed(6);
                longitudeField.value = position.coords.longitude.toFixed(6);
                
                // Optionally reverse geocode to fill in address fields
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.address) {
                            if (!addressField.value && data.address.road) {
                                addressField.value = `${data.address.road}${data.address.house_number ? ' ' + data.address.house_number : ''}`;
                            }
                            if (!cityField.value && data.address.city) {
                                cityField.value = data.address.city;
                            } else if (!cityField.value && data.address.town) {
                                cityField.value = data.address.town;
                            }
                            if (!stateField.value && data.address.state) {
                                stateField.value = data.address.state;
                            }
                            if (!zipField.value && data.address.postcode) {
                                zipField.value = data.address.postcode;
                            }
                        }
                    })
                    .catch(error => console.error('Geocoding error:', error));
            }, function(error) {
                console.error('Geolocation error:', error);
            });
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>