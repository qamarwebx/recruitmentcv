@props([
    'id' => null,
    'name',
    'label' => '',
    'value' => '',
    'bg' => '#ffffff',  // optional: default white
])

@php
    $editorId = $id ? $id : uniqid('quill_');
@endphp

<div id="{{ $editorId }}" class="quill-wrapper mb-3">

    @if (!empty($label))
        <label class="form-label">{{ $label }}</label>
    @endif

    <!-- Toolbar -->
    <div class="toolbar-container"></div>

    <!-- Quill Editor -->
    <div class="quill-editor">{!! $value !!}</div>

    <!-- Background Picker -->
    <div class="bg-tools mt-2">
        <label>Background:</label>
        <input type="color" class="bgColorPicker" value="{{ $bg ?? '#ffffff' }}">
        <button type="button" class="resetBgBtn reset-btn" title="Reset background">⟳</button>
    </div>

    <!-- Hidden input: HTML content -->
    <textarea name="{{ $name }}" class="quill-content" hidden>{!! $value !!}</textarea>

    <!-- NEW: Hidden input for background -->
    <input type="hidden" name="{{ $name }}_bg" class="quill-bg" value="{{ $bg ?? '#ffffff' }}">

</div>



<!-- <div class="quill-wrapper mb-3">
  @if (!empty($label))
    <label class="form-label">{{ $label }}</label>
  @endif

  <div class="toolbar-container"></div>

  <div class="quill-editor">{!! $value ?? '' !!}</div>

  <div class="bg-tools mt-2">
    <label>Background:</label>
    <input type="color" class="bgColorPicker" value="#ffffff" />
    <button type="button" class="resetBgBtn reset-btn" title="Reset background">⟳</button>
  </div>

  <textarea name="{{ $name }}" class="quill-content" hidden>{!! $value ?? '' !!}</textarea>
</div> -->


 <!-- Toolbar -->
  <!-- <div id="toolbar-container" class="toolbar-container">
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
      <input type="color" class="bgColorPicker" id="bgColorPicker" value="#ffffff" />
      <button id="resetBgBtn" class="reset-btn resetBgBtn" title="Reset background">
        <svg viewBox="0 0 18 18">
          <polyline class="ql-stroke" points="8 3 9 3 9 8 14 8"></polyline>
          <path class="ql-stroke" d="M14 8A6 6 0 1 1 9 3"></path>
        </svg>
      </button>
    </span>
  </div> -->

  <!-- Editor -->
  <!-- <div class="quill-editor"></div> -->