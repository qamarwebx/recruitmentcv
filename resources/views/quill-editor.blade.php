<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quill Editor</title>

  <!-- Quill + Highlight.js + KaTeX -->
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" />

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 20px;
    }

    #toolbar-container {
      border: 1px solid #ccc;
      border-bottom: none;
      border-radius: 6px 6px 0 0;
      background: #f9f9f9;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      width: 50%;
    }

    #quill-editor {
      height: 10%;
      width: 50%;
      border: 1px solid #ccc;
      border-radius: 0 0 6px 6px;
      background: #ffffff;
      transition: background-color 0.3s ease-in-out;
    }

    .ql-toolbar button svg {
      width: 16px;
      height: 16px;
    }

    /* Background color picker + reset */
    .bg-tools {
      display: flex;
      align-items: center;
      margin-left: auto;
      gap: 8px;
    }
    .bg-tools label {
      font-size: 13px;
      color: #333;
    }
    .bg-tools input[type="color"] {
      width: 36px;
      height: 26px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      background: transparent;
    }

    /* Reset button */
    .reset-btn {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f3f3;
      border: 1px solid #ccc;
      border-radius: 4px;
      cursor: pointer;
      padding: 0;
      transition: all 0.2s ease;
    }
    .reset-btn svg {
      width: 14px;
      height: 14px;
      stroke: #333;
      stroke-width: 2;
      fill: none;
    }
    .reset-btn:hover {
      background: #e0e0e0;
    }

  </style>
</head>
<body>

  <!-- Toolbar -->
  <div id="toolbar-container">
    <span class="ql-formats">
      <select class="ql-font"></select>
      <select class="ql-size"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-bold"></button>
      <button class="ql-italic"></button>
      <button class="ql-underline"></button>
      <button class="ql-strike"></button>
    </span>
    <span class="ql-formats">
      <select class="ql-color"></select>
      <select class="ql-background"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-script" value="sub"></button>
      <button class="ql-script" value="super"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-header" value="1"></button>
      <button class="ql-header" value="2"></button>
      <button class="ql-blockquote"></button>
      <button class="ql-code-block"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-list" value="ordered"></button>
      <button class="ql-list" value="bullet"></button>
      <button class="ql-indent" value="-1"></button>
      <button class="ql-indent" value="+1"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-direction" value="rtl"></button>
      <select class="ql-align"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-link"></button>
      <button class="ql-image"></button>
      <button class="ql-video"></button>
      <button class="ql-formula"></button>
    </span>

    <span class="ql-formats">
      <button class="ql-insertTable">
        <svg viewBox="0 0 18 18">
          <rect class="ql-stroke" height="10" width="10" x="4" y="4"></rect>
          <line class="ql-stroke" x1="9" x2="9" y1="4" y2="14"></line>
          <line class="ql-stroke" x1="4" x2="14" y1="9" y2="9"></line>
        </svg>
      </button>
      <button class="ql-insertRowAbove">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="5 7 9 3 13 7"></polyline>
          <line class="ql-stroke" x1="9" x2="9" y1="3" y2="13"></line>
        </svg>
      </button>
      <button class="ql-insertRowBelow">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="5 11 9 15 13 11"></polyline>
          <line class="ql-stroke" x1="9" x2="9" y1="5" y2="15"></line>
        </svg>
      </button>
      <button class="ql-insertColumnLeft">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="7 5 3 9 7 13"></polyline>
          <line class="ql-stroke" x1="3" x2="13" y1="9" y2="9"></line>
        </svg>
      </button>
      <button class="ql-insertColumnRight">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="11 5 15 9 11 13"></polyline>
          <line class="ql-stroke" x1="5" x2="15" y1="9" y2="9"></line>
        </svg>
      </button>
      <button class="ql-deleteTable">
        <svg viewBox="0 0 18 18">
          <rect class="ql-stroke" height="10" width="10" x="4" y="4"></rect>
          <line class="ql-stroke" x1="5" x2="13" y1="5" y2="13"></line>
          <line class="ql-stroke" x1="13" x2="5" y1="5" y2="13"></line>
        </svg>
      </button>
    </span>

    <span class="bg-tools">
      <label for="bgColorPicker">Editor Background:</label>
      <input type="color" id="bgColorPicker" value="#ffffff" />
      <button id="resetBgBtn" class="reset-btn" title="Reset background">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="8 3 9 3 9 8 14 8"></polyline>
          <path class="ql-stroke" d="M14 8A6 6 0 1 1 9 3"></path>
        </svg>
      </button>
    </span>
  </div>

  <!-- Editor -->
  <div id="editor"></div>


  <!-- Initialize Quill -->
  <script>
    const quill = new Quill("#quill-editor", {
      modules: {
        syntax: true,
        toolbar: {
          container: "#toolbar-container",
          handlers: {
            insertTable() {
              quill.getModule("table").insertTable(3, 3);
            },
            insertRowAbove() {
              quill.getModule("table").insertRowAbove();
            },
            insertRowBelow() {
              quill.getModule("table").insertRowBelow();
            },
            insertColumnLeft() {
              quill.getModule("table").insertColumnLeft();
            },
            insertColumnRight() {
              quill.getModule("table").insertColumnRight();
            },
            deleteTable() {
              quill.getModule("table").deleteTable();
            },
          },
        },
        table: true,
      },
      placeholder: "Compose an epic...",
      theme: "snow",
    });

    const editor = document.getElementById("editor");
    const bgColorPicker = document.getElementById("bgColorPicker");
    const resetBgBtn = document.getElementById("resetBgBtn");

    // Load saved color
    const savedColor = localStorage.getItem("editorBgColor") || "#ffffff";
    editor.style.backgroundColor = savedColor;
    bgColorPicker.value = savedColor;

    // Change background dynamically
    bgColorPicker.addEventListener("input", (e) => {
      const color = e.target.value;
      editor.style.backgroundColor = color;
      localStorage.setItem("editorBgColor", color);
    });

    // Reset both editor + preview
    resetBgBtn.addEventListener("click", () => {
      editor.style.backgroundColor = "#ffffff";
      bgColorPicker.value = "#ffffff";
      localStorage.removeItem("editorBgColor");
    });


  </script>
</body>
</html>
