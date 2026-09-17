<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Template Editor - TinyMCE (Cloud + API Key)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap (for layout only) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ✅ Load TinyMCE from Tiny Cloud using your API key -->
  <script src="https://cdn.tiny.cloud/1/89mpfhvgionctwpt63zim4yzq0iccxg75g5850w5e35tndas/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

  <style>
    body { background:#f8f9fa; padding: 30px; }
    .tox-tinymce { min-height: 500px; }
    .color-picker-box {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="container">
  <h3 class="mb-3">📧 Email Template Editor</h3>
  <p class="text-muted">You’re using TinyMCE Cloud (with API key) — now supports dynamic background color!</p>

  <!-- 🎨 Background Color Control -->
  <div class="color-picker-box">
    <label for="bgColorPicker" class="form-label fw-bold">Background Color:</label>
    <input type="color" id="bgColorPicker" class="form-control form-control-color" value="#ffffff" title="Choose background color">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetEditorBg()">Reset</button>
  </div>

  <!-- 📝 The Editor -->
  <form>
    <textarea id="email_editor">
      <h2 style="text-align:center;">Welcome to QamarHire!</h2>
      <p>Dear <strong>@{{NAME}}</strong>,</p>
      <p>We’re glad to have you! You can edit this email content freely using the editor below.</p>
      <p>— The QamarHire Team</p>
    </textarea>
  </form>
</div>

<script>
tinymce.init({
  selector: '#email_editor',
  height: 500,
  menubar: false,
  branding: false,
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount'
  ],
  toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | ' +
           'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
           'table | link image | code preview',

  // Default editor style
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; background-color:#ffffff; }',

  // Allow base64 image embedding (for demo)
  paste_data_images: true,

  file_picker_types: 'image',
  file_picker_callback: (callback, value, meta) => {
    if (meta.filetype === 'image') {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.onchange = function() {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = function() {
          callback(reader.result, { alt: file.name });
        };
        reader.readAsDataURL(file);
      };
      input.click();
    }
  },

  setup: function (editor) {
    editor.on('init', function () {
      // Set initial background from color picker
      const picker = document.getElementById('bgColorPicker');
      editor.getBody().style.backgroundColor = picker.value;

      // Change dynamically when picker changes
      picker.addEventListener('input', function () {
        editor.getBody().style.backgroundColor = this.value;
      });
    });
  }
});

// 🎨 Reset background to white
function resetEditorBg() {
  const editor = tinymce.get('email_editor');
  if (editor) {
    editor.getBody().style.backgroundColor = '#ffffff';
    document.getElementById('bgColorPicker').value = '#ffffff';
  }
}
</script>

</body>
</html>
