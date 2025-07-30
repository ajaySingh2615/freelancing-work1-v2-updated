<?php
/**
 * Cloudinary Helper Functions
 * MedStudy Global - Blog System
 */

require_once '../config/database.php';

/**
 * Upload image to Cloudinary
 */
function uploadToCloudinary($file, $folder = 'blog-images') {
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;
    
    // Validate file
    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        return ['error' => 'No file provided'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.'];
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'File too large. Maximum size is 5MB.'];
    }
    
    try {
        $timestamp = time();
        $publicId = $folder . '/' . uniqid() . '_' . $timestamp;
        
        // Generate signature
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'folder' => $folder
        ];
        
        ksort($params);
        $stringToSign = '';
        foreach ($params as $key => $value) {
            $stringToSign .= $key . '=' . $value . '&';
        }
        $stringToSign = rtrim($stringToSign, '&') . $apiSecret;
        
        $signature = sha1($stringToSign);
        
        // Prepare upload data
        $uploadData = [
            'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
            'folder' => $folder
        ];
        
        // Upload to Cloudinary
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            return ['error' => 'Upload failed: ' . curl_error($ch)];
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        // Debug logging
        if ($httpCode !== 200) {
            error_log("Cloudinary upload failed - HTTP Code: $httpCode, Response: " . $response);
        }
        
        if ($httpCode === 200 && isset($result['secure_url'])) {
            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
                'bytes' => $result['bytes']
            ];
        } else {
            $errorMessage = 'Upload failed';
            if (isset($result['error']['message'])) {
                $errorMessage = $result['error']['message'];
            } elseif (isset($result['error'])) {
                $errorMessage = is_array($result['error']) ? json_encode($result['error']) : $result['error'];
            }
            return ['error' => $errorMessage];
        }
        
    } catch (Exception $e) {
        return ['error' => 'Upload error: ' . $e->getMessage()];
    }
}

/**
 * Delete image from Cloudinary
 */
function deleteFromCloudinary($publicId) {
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;
    
    try {
        $timestamp = time();
        
        // Generate signature
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp
        ];
        
        ksort($params);
        $stringToSign = '';
        foreach ($params as $key => $value) {
            $stringToSign .= $key . '=' . $value . '&';
        }
        $stringToSign = rtrim($stringToSign, '&') . $apiSecret;
        
        $signature = sha1($stringToSign);
        
        // Prepare delete data
        $deleteData = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature
        ];
        
        // Delete from Cloudinary
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $deleteData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if ($httpCode === 200 && $result['result'] === 'ok') {
            return ['success' => true];
        } else {
            return ['error' => 'Delete failed'];
        }
        
    } catch (Exception $e) {
        return ['error' => 'Delete error: ' . $e->getMessage()];
    }
}

/**
 * Get optimized image URL with transformations
 */
function getOptimizedImageUrl($url, $width = null, $height = null, $quality = 'auto') {
    if (empty($url)) return '';
    
    // Check if it's a Cloudinary URL
    if (strpos($url, 'cloudinary.com') === false) {
        return $url;
    }
    
    $transformations = [];
    
    if ($width) {
        $transformations[] = "w_{$width}";
    }
    
    if ($height) {
        $transformations[] = "h_{$height}";
    }
    
    if ($quality) {
        $transformations[] = "q_{$quality}";
    }
    
    // Add default optimizations
    $transformations[] = "f_auto";
    $transformations[] = "c_fill";
    $transformations[] = "g_auto";
    
    if (!empty($transformations)) {
        $transformString = implode(',', $transformations);
        return str_replace('/upload/', "/upload/{$transformString}/", $url);
    }
    
    return $url;
}

/**
 * Generate responsive image URLs
 */
function getResponsiveImageUrls($url) {
    if (empty($url)) return [];
    
    return [
        'thumbnail' => getOptimizedImageUrl($url, 150, 150),
        'small' => getOptimizedImageUrl($url, 300, 200),
        'medium' => getOptimizedImageUrl($url, 600, 400),
        'large' => getOptimizedImageUrl($url, 1200, 800),
        'hero' => getOptimizedImageUrl($url, 1600, 900)
    ];
}

/**
 * Validate image file
 */
function validateImageFile($file) {
    $errors = [];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        $errors[] = 'No file uploaded';
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = 'File too large. Maximum size is 5MB';
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        $errors[] = 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed';
    }
    
    // Check image dimensions
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'Invalid image file';
    } else {
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < 300 || $height < 200) {
            $errors[] = 'Image too small. Minimum size is 300x200 pixels';
        }
        
        if ($width > 5000 || $height > 5000) {
            $errors[] = 'Image too large. Maximum size is 5000x5000 pixels';
        }
    }
    
    return $errors;
}

/**
 * Generate image alt text based on filename and context
 */
function generateImageAltText($filename, $context = '') {
    $filename = pathinfo($filename, PATHINFO_FILENAME);
    $filename = str_replace(['-', '_'], ' ', $filename);
    $filename = ucwords($filename);
    
    if ($context) {
        return $context . ' - ' . $filename;
    }
    
    return $filename;
}

/**
 * Upload university featured image
 */
function uploadUniversityFeaturedImage($file, $universitySlug) {
    $folder = 'universities/featured';
    $result = uploadToCloudinary($file, $folder);
    
    if (isset($result['success'])) {
        // Add university-specific metadata
        $result['alt_text'] = generateImageAltText($file['name'], $universitySlug . ' university featured image');
        $result['category'] = 'featured';
    }
    
    return $result;
}

/**
 * Upload university logo image
 */
function uploadUniversityLogo($file, $universitySlug) {
    $folder = 'universities/logos';
    $result = uploadToCloudinary($file, $folder);
    
    if (isset($result['success'])) {
        // Add university-specific metadata
        $result['alt_text'] = generateImageAltText($file['name'], $universitySlug . ' university logo');
        $result['category'] = 'logo';
    }
    
    return $result;
}

/**
 * Upload university gallery image
 */
function uploadUniversityGalleryImage($file, $universitySlug) {
    $folder = 'universities/gallery';
    $result = uploadToCloudinary($file, $folder);
    
    if (isset($result['success'])) {
        // Add university-specific metadata
        $result['alt_text'] = generateImageAltText($file['name'], $universitySlug . ' university campus');
        $result['category'] = 'gallery';
    }
    
    return $result;
}

/**
 * Upload country featured image
 */
function uploadCountryFeaturedImage($file, $countrySlug) {
    $folder = 'countries/featured';
    $result = uploadToCloudinary($file, $folder);
    
    if (isset($result['success'])) {
        // Add country-specific metadata
        $result['alt_text'] = generateImageAltText($file['name'], 'Study in ' . ucfirst($countrySlug));
        $result['category'] = 'country';
    }
    
    return $result;
}

/**
 * Bulk upload university images
 */
function bulkUploadUniversityImages($files, $universitySlug) {
    global $db;
    
    if (!is_array($files['tmp_name'])) {
        return ['error' => 'No files provided for bulk upload'];
    }
    
    $results = [];
    $successCount = 0;
    $errorCount = 0;
    
    for ($i = 0; $i < count($files['tmp_name']); $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];
        
        // Skip if file has error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $results[] = [
                'filename' => $file['name'],
                'success' => false,
                'error' => 'Upload error code: ' . $file['error']
            ];
            $errorCount++;
            continue;
        }
        
        // Upload to Cloudinary
        $uploadResult = uploadUniversityGalleryImage($file, $universitySlug);
        
        if (isset($uploadResult['success'])) {
            // Get university ID
            $stmt = $db->prepare("SELECT id FROM universities WHERE slug = ? LIMIT 1");
            $stmt->execute([$universitySlug]);
            $university = $stmt->fetch();
            
            if ($university) {
                // Insert into university_images table
                $imageStmt = $db->prepare("INSERT INTO university_images (university_id, image_url) VALUES (?, ?)");
                $imageStmt->execute([$university['id'], $uploadResult['url']]);
                
                $results[] = [
                    'filename' => $file['name'],
                    'success' => true,
                    'url' => $uploadResult['url'],
                    'public_id' => $uploadResult['public_id']
                ];
                $successCount++;
            } else {
                $results[] = [
                    'filename' => $file['name'],
                    'success' => false,
                    'error' => 'University not found'
                ];
                $errorCount++;
            }
        } else {
            $results[] = [
                'filename' => $file['name'],
                'success' => false,
                'error' => $uploadResult['error'] ?? 'Unknown upload error'
            ];
            $errorCount++;
        }
    }
    
    return [
        'success' => $successCount > 0,
        'summary' => [
            'total' => count($files['tmp_name']),
            'success' => $successCount,
            'errors' => $errorCount
        ],
        'results' => $results
    ];
}

/**
 * Get university image presets for different use cases
 */
function getUniversityImagePresets($url) {
    if (empty($url)) return [];
    
    return [
        // Featured image presets
        'hero_desktop' => getOptimizedImageUrl($url, 1600, 900, 'auto'),
        'hero_tablet' => getOptimizedImageUrl($url, 1024, 576, 'auto'),
        'hero_mobile' => getOptimizedImageUrl($url, 768, 432, 'auto'),
        
        // Card/listing presets
        'card_large' => getOptimizedImageUrl($url, 600, 400, 'auto'),
        'card_medium' => getOptimizedImageUrl($url, 400, 267, 'auto'),
        'card_small' => getOptimizedImageUrl($url, 300, 200, 'auto'),
        
        // Logo presets
        'logo_large' => getOptimizedImageUrl($url, 200, 200, 'auto'),
        'logo_medium' => getOptimizedImageUrl($url, 100, 100, 'auto'),
        'logo_small' => getOptimizedImageUrl($url, 50, 50, 'auto'),
        
        // Gallery presets
        'gallery_main' => getOptimizedImageUrl($url, 800, 600, 'auto'),
        'gallery_thumb' => getOptimizedImageUrl($url, 150, 150, 'auto'),
        
        // Original
        'original' => $url
    ];
}

/**
 * Delete university images from both Cloudinary and database
 */
function deleteUniversityImage($imageId) {
    global $db;
    
    try {
        // Get image details from database
        $stmt = $db->prepare("SELECT * FROM university_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if (!$image) {
            return ['error' => 'Image not found'];
        }
        
        // Extract public_id from Cloudinary URL
        $publicId = extractPublicIdFromUrl($image['image_url']);
        
        if ($publicId) {
            // Delete from Cloudinary
            $cloudinaryResult = deleteFromCloudinary($publicId);
            
            if (!isset($cloudinaryResult['success'])) {
                // Log warning but continue with database deletion
                error_log("Failed to delete from Cloudinary: " . ($cloudinaryResult['error'] ?? 'Unknown error'));
            }
        }
        
        // Delete from database
        $deleteStmt = $db->prepare("DELETE FROM university_images WHERE id = ?");
        $deleteStmt->execute([$imageId]);
        
        return ['success' => true, 'message' => 'Image deleted successfully'];
        
    } catch (Exception $e) {
        return ['error' => 'Delete error: ' . $e->getMessage()];
    }
}

/**
 * Extract public_id from Cloudinary URL
 */
function extractPublicIdFromUrl($url) {
    if (strpos($url, 'cloudinary.com') === false) {
        return null;
    }
    
    // Extract the public_id from URL
    // Example URL: https://res.cloudinary.com/cloud/image/upload/v1234567890/folder/image.jpg
    preg_match('/\/upload\/(?:v\d+\/)?(.+)\.([^.]+)$/', $url, $matches);
    
    if (isset($matches[1])) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Optimize image upload settings for universities
 */
function getUniversityUploadSettings() {
    return [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'min_dimensions' => ['width' => 400, 'height' => 300],
        'max_dimensions' => ['width' => 5000, 'height' => 5000],
        'quality' => 'auto:best',
        'format' => 'auto'
    ];
}

/**
 * Validate university image upload
 */
function validateUniversityImageUpload($file, $type = 'gallery') {
    $errors = validateImageFile($file);
    $settings = getUniversityUploadSettings();
    
    // Additional validation for specific image types
    if (empty($errors)) {
        $imageInfo = getimagesize($file['tmp_name']);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        switch ($type) {
            case 'logo':
                if ($width < 100 || $height < 100) {
                    $errors[] = 'Logo image too small. Minimum size is 100x100 pixels';
                }
                break;
                
            case 'featured':
                if ($width < 800 || $height < 400) {
                    $errors[] = 'Featured image too small. Minimum size is 800x400 pixels';
                }
                // Aspect ratio check (prefer 16:9 or 4:3)
                $ratio = $width / $height;
                if ($ratio < 1.3 || $ratio > 2.0) {
                    $errors[] = 'Featured image should have aspect ratio between 4:3 and 2:1';
                }
                break;
                
            case 'gallery':
                if ($width < 400 || $height < 300) {
                    $errors[] = 'Gallery image too small. Minimum size is 400x300 pixels';
                }
                break;
        }
    }
    
    return $errors;
}
?> 