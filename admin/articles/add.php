<?php
/**
 * VIBEDAYBKK Admin - Add Article
 * เพิ่มบทความใหม่
 */

define('VIBEDAYBKK_ADMIN', true);
require_once '../../includes/config.php';


// Permission check
require_permission('articles', 'create');
$page_title = 'เพิ่มบทความใหม่';
$current_page = 'articles';

// Get active categories
$categories = db_get_rows($conn, "SELECT * FROM article_categories WHERE status = 'active' ORDER BY sort_order ASC");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = clean_input($_POST['title']);
        $slug = !empty($_POST['slug']) ? generate_slug($_POST['slug']) : generate_slug($title);
        
        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_image($_FILES['featured_image'], 'articles');
            if ($upload_result['success']) {
                $featured_image = $upload_result['file_path'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }
        
        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => clean_input($_POST['excerpt']),
            'content' => $_POST['content'],
            'featured_image' => $featured_image,
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'author_id' => $_SESSION['user_id'],
            'read_time' => !empty($_POST['read_time']) ? (int)$_POST['read_time'] : 5,
            'status' => $_POST['status'],
            'published_at' => $_POST['status'] == 'published' ? date('Y-m-d H:i:s') : null
        ];
        
        if (empty($title)) {
            $errors[] = 'กรุณากรอกหัวข้อบทความ';
        }
        
        // Check duplicate slug
        $stmt = $conn->prepare("SELECT id FROM articles WHERE slug = ?");
        $stmt->bind_param('s', $slug);
            $stmt->execute();
        if ($stmt->fetch()) {
            $errors[] = 'URL นี้มีอยู่แล้ว กรุณาเปลี่ยน';
        }
        
        if (empty($errors)) {
            try {
                // ตรวจสอบว่าตารางมีคอลัมน์อะไรบ้าง
                $columns_result = $conn->query("DESCRIBE articles");
                $existing_columns = [];
                while ($col = $columns_result->fetch_assoc()) {
                    $existing_columns[] = $col['Field'];
                }
                
                // กรองเฉพาะคอลัมน์ที่มีอยู่จริง
                $filtered_data = [];
                foreach ($data as $key => $value) {
                    if (in_array($key, $existing_columns)) {
                        $filtered_data[$key] = $value;
                    }
                }
                
                // เพิ่ม created_at ถ้ามี
                if (in_array('created_at', $existing_columns)) {
                    $filtered_data['created_at'] = date('Y-m-d H:i:s');
                }
                
                if (db_insert($conn, 'articles', $filtered_data)) {
                    $article_id = $conn->insert_id;
                    log_activity($conn, $_SESSION['user_id'], 'create', 'articles', $article_id, null, $filtered_data);
                    set_message('success', 'เพิ่มบทความสำเร็จ');
                    redirect(ADMIN_URL . '/articles/edit.php?id=' . $article_id);
                } else {
                    $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error;
                }
            } catch (Exception $e) {
                $errors[] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-900 flex items-center">
        <i class="fas fa-plus-circle mr-3 text-green-600"></i>เพิ่มบทความใหม่
    </h2>
    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>กลับ
    </a>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <h5 class="font-bold mb-2 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>พบข้อผิดพลาด:
    </h5>
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Content -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-edit mr-3"></i>เนื้อหาบทความ
                    </h5>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            หัวข้อบทความ <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="title" required 
                               class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL (Slug)</label>
                        <input type="text" name="slug" placeholder="auto-generate จากหัวข้อ" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <p class="text-sm text-gray-500 mt-2">ถ้าไม่กรอก จะสร้างอัตโนมัติจากหัวข้อ</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">คำอธิบายสั้น (Excerpt)</label>
                        <textarea name="excerpt" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            เนื้อหาบทความ <span class="text-red-600">*</span>
                        </label>
                        <textarea name="content" id="editor" rows="15" required 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <!-- Publish -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-paper-plane mr-3"></i>เผยแพร่
                    </h5>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">สถานะ</label>
                        <select name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                            <option value="draft">แบบร่าง</option>
                            <option value="published">เผยแพร่ทันที</option>
                            <option value="archived">เก็บถาวร</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i>บันทึก
                    </button>
                </div>
            </div>
            
            <!-- Featured Image -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-image mr-3"></i>รูปภาพหลัก
                    </h5>
                </div>
                <div class="p-6">
                    <input type="file" name="featured_image" accept="image/*" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-sm text-gray-500 mt-2">แนะนำขนาด 1200x630px</p>
                </div>
            </div>
            
            <!-- Meta -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
                    <h5 class="text-white text-lg font-semibold flex items-center">
                        <i class="fas fa-tags mr-3"></i>ข้อมูลเพิ่มเติม
                    </h5>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">หมวดหมู่</label>
                        <select name="category_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php if ($cat['icon']): ?>
                                    <?php echo $cat['icon']; ?> 
                                <?php endif; ?>
                                <?php echo $cat['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-sm text-gray-500 mt-2">
                            <a href="<?php echo ADMIN_URL; ?>/article-categories/" class="text-orange-600 hover:underline" target="_blank">
                                <i class="fas fa-external-link-alt mr-1"></i>จัดการหมวดหมู่
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">เวลาอ่าน (นาที)</label>
                        <input type="number" name="read_time" value="5" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
class UploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('upload', file);

            fetch('upload-image.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.url) {
                    resolve({
                        default: result.url
                    });
                } else {
                    reject(result.error?.message || 'เกิดข้อผิดพลาดในการอัพโหลด');
                }
            })
            .catch(error => {
                reject('เกิดข้อผิดพลาด: ' + error);
            });
        }));
    }

    abort() {
        // Reject the promise returned from the upload() method.
    }
}

function CustomUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new UploadAdapter(loader);
    };
}

ClassicEditor
    .create(document.querySelector('#editor'), {
        extraPlugins: [CustomUploadAdapterPlugin],
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'numberedList', 'bulletedList', '|',
                'outdent', 'indent', '|',
                'link', 'blockQuote', 'insertTable', '|',
                'imageUpload', 'mediaEmbed', '|',
                'undo', 'redo', '|',
                'code', 'codeBlock', 'horizontalLine', '|',
                'removeFormat'
            ],
            shouldNotGroupWhenFull: true
        },
        language: 'th',
        image: {
            toolbar: [
                'imageTextAlternative', '|',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side', '|',
                'linkImage'
            ],
            styles: [
                'full',
                'side',
                'alignLeft',
                'alignCenter',
                'alignRight'
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
                'tableCellProperties',
                'tableProperties'
            ]
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'ย่อหน้า', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'หัวข้อ 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'หัวข้อ 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'หัวข้อ 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'หัวข้อ 4', class: 'ck-heading_heading4' }
            ]
        },
        fontSize: {
            options: [
                'tiny',
                'small',
                'default',
                'big',
                'huge'
            ]
        },
        link: {
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'เปิดในแท็บใหม่',
                    attributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }
                }
            }
        }
    })
    .then(editor => {
        window.editor = editor;
        
        // Set minimum height
        editor.editing.view.change(writer => {
            writer.setStyle('min-height', '400px', editor.editing.view.document.getRoot());
        });
        
        console.log('✅ CKEditor 5 พร้อมระบบอัพโหลดรูปภาพ!');
    })
    .catch(error => {
        console.error('❌ Error initializing CKEditor:', error);
    });
</script>

<style>
.ck-editor__editable {
    min-height: 400px;
}
.ck.ck-editor__main > .ck-editor__editable {
    background-color: #fff;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}
.ck.ck-editor__main > .ck-editor__editable:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>



