@extends('layout.admin.admin_layout')

@section('title', 'File Manager')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <style>
        :root {
            --fm-radius: .5rem;
        }
        .fm-app { min-height: 78vh; }

        /* Storage header */
        .fm-storage-card { border-radius: var(--fm-radius); }
        .fm-storage-bar { height: 10px; border-radius: 999px; overflow: hidden; background: rgba(var(--bs-light-rgb), 1); }
        .fm-storage-bar .fm-storage-fill { height: 100%; border-radius: 999px; transition: width .4s ease; background: linear-gradient(90deg,#7367f0,#9e95f5); }
        .fm-storage-bar.fm-storage-warn .fm-storage-fill { background: linear-gradient(90deg,#ff9f43,#ffc078); }
        .fm-storage-bar.fm-storage-danger .fm-storage-fill { background: linear-gradient(90deg,#ea5455,#f28b82); }

        /* Layout */
        .fm-body { display: flex; gap: 1rem; align-items: flex-start; }
        .fm-sidebar {
            width: 250px; flex: 0 0 250px; background: var(--bs-card-bg, #fff);
            border-radius: var(--fm-radius); border: 1px solid var(--bs-border-color, #e5e7eb);
            padding: .75rem; position: sticky; top: .75rem; max-height: calc(100vh - 120px); overflow-y: auto;
        }
        .fm-main { flex: 1 1 auto; min-width: 0; }
        .fm-nav-link {
            display: flex; align-items: center; gap: .55rem; padding: .5rem .65rem; border-radius: .45rem;
            color: inherit; text-decoration: none; font-size: .9rem; cursor: pointer;
        }
        .fm-nav-link:hover { background: rgba(115,103,240,.08); }
        .fm-nav-link.active { background: rgba(115,103,240,.14); color: #7367f0; font-weight: 600; }
        .fm-nav-link .badge-count { margin-left: auto; font-size: .7rem; }
        .fm-nav-link.fm-drop-hover { outline: 2px dashed #7367f0; outline-offset: 2px; }

        .fm-tree-toggle { width: 18px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .fm-tree-children { padding-left: 1.1rem; display: none; }
        .fm-tree-children.expanded { display: block; }
        .fm-tree-item.fm-drop-hover > .fm-nav-link { outline: 2px dashed #7367f0; outline-offset: 1px; background: rgba(115,103,240,.12); }

        @media (max-width: 991.98px) {
            .fm-sidebar {
                position: fixed; left: -280px; top: 0; bottom: 0; width: 270px; z-index: 1050;
                transition: left .25s ease; box-shadow: 0 0 24px rgba(0,0,0,.2); max-height: 100vh;
            }
            .fm-sidebar.fm-open { left: 0; }
            .fm-sidebar-backdrop {
                display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 1049;
            }
            .fm-sidebar-backdrop.fm-open { display: block; }
        }

        /* Toolbar */
        .fm-toolbar { display: flex; flex-wrap: wrap; gap: .6rem; align-items: center; justify-content: space-between; }
        .fm-breadcrumb { margin: 0; flex-wrap: wrap; }
        .fm-breadcrumb .breadcrumb-item a { cursor: pointer; }
        .fm-quick-chips { display: flex; gap: .4rem; flex-wrap: wrap; margin: .75rem 0; }
        .fm-chip {
            border: 1px solid var(--bs-border-color, #e5e7eb); border-radius: 999px; padding: .3rem .8rem;
            font-size: .8rem; cursor: pointer; background: transparent; white-space: nowrap; display: inline-flex; align-items: center; gap: .35rem;
        }
        .fm-chip:hover { background: rgba(115,103,240,.08); }
        .fm-chip.active { background: #7367f0; color: #fff; border-color: #7367f0; }
        .fm-chip .cnt { opacity: .75; font-size: .72rem; }

        /* Selection bar */
        .fm-selection-bar {
            display: flex; align-items: center; gap: .75rem; background: rgba(115,103,240,.1);
            border: 1px solid rgba(115,103,240,.25); border-radius: var(--fm-radius); padding: .5rem .9rem; margin-bottom: .75rem;
        }

        /* Card grid */
        .fm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .9rem; }
        .fm-card {
            position: relative; border: 1px solid var(--bs-border-color, #e5e7eb); border-radius: var(--fm-radius);
            padding: .85rem .7rem .6rem; background: var(--bs-card-bg, #fff); cursor: pointer; user-select: none;
            transition: box-shadow .15s ease, border-color .15s ease, transform .1s ease;
        }
        .fm-card:hover { box-shadow: 0 .25rem .75rem rgba(0,0,0,.08); }
        .fm-card.fm-selected { border-color: #7367f0; background: rgba(115,103,240,.06); box-shadow: 0 0 0 2px rgba(115,103,240,.35); }
        .fm-card.fm-drop-hover { outline: 2px dashed #7367f0; outline-offset: 2px; }
        .fm-card.fm-dragging { opacity: .45; }
        .fm-card-check {
            position: absolute; top: .5rem; left: .5rem; opacity: 0; transition: opacity .1s;
        }
        .fm-card:hover .fm-card-check, .fm-card.fm-selected .fm-card-check { opacity: 1; }
        .fm-card-menu-btn { position: absolute; top: .35rem; right: .35rem; opacity: 0; transition: opacity .1s; }
        .fm-card:hover .fm-card-menu-btn, .fm-card-menu-btn.show { opacity: 1; }
        .fm-card-fav { position: absolute; top: .35rem; right: 2.1rem; opacity: 0; transition: opacity .1s; color: #d9d9d9; }
        .fm-card:hover .fm-card-fav, .fm-card-fav.fm-active { opacity: 1; }
        .fm-card-fav.fm-active { color: #ffc107; }
        .fm-icon-tile {
            width: 100%; height: 78px; border-radius: .4rem; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; letter-spacing: .03em; color: #fff; overflow: hidden; margin-bottom: .5rem;
        }
        .fm-icon-tile img { width: 100%; height: 100%; object-fit: cover; }
        .fm-icon-tile i { font-size: 1.9rem; }
        .fm-card-name { font-size: .82rem; font-weight: 600; line-height: 1.25; word-break: break-word; max-height: 2.5em; overflow: hidden; }
        .fm-card-meta { font-size: .72rem; color: rgba(var(--bs-body-color-rgb), .65); margin-top: .15rem; }

        /* List view */
        .fm-list .fm-grid { display: block; }
        .fm-list .fm-card {
            display: flex; align-items: center; gap: .75rem; padding: .5rem .7rem; margin-bottom: .4rem; border-radius: .4rem;
        }
        .fm-list .fm-icon-tile { width: 40px; height: 40px; margin-bottom: 0; flex: 0 0 40px; font-size: .6rem; }
        .fm-list .fm-icon-tile i { font-size: 1.1rem; }
        .fm-list .fm-card-body { flex: 1 1 auto; display: flex; align-items: center; gap: 1.25rem; min-width: 0; }
        .fm-list .fm-card-name { flex: 1 1 260px; min-width: 0; }
        .fm-list .fm-card-meta { flex: 0 0 auto; margin-top: 0; width: 110px; }
        .fm-list .fm-card-check { position: static; opacity: 1; }
        .fm-list .fm-card-fav, .fm-list .fm-card-menu-btn { position: static; opacity: 1; }

        /* Empty states */
        .fm-empty-state { text-align: center; padding: 3.5rem 1rem; color: rgba(var(--bs-body-color-rgb), .65); }
        .fm-empty-state i { font-size: 3rem; opacity: .35; }

        /* Drag overlay */
        #fm-drag-overlay {
            position: fixed; inset: 0; z-index: 1080; background: rgba(115,103,240,.14); backdrop-filter: blur(1px);
            border: 3px dashed #7367f0; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: .5rem;
        }
        #fm-drag-overlay .fm-drop-badge {
            background: var(--bs-card-bg, #fff); border-radius: 1rem; padding: 2rem 3rem; box-shadow: 0 1rem 3rem rgba(0,0,0,.15); text-align: center;
        }

        /* Upload queue */
        .fm-upload-row { display: flex; align-items: center; gap: .6rem; padding: .5rem 0; border-bottom: 1px solid var(--bs-border-color, #eee); }
        .fm-upload-row:last-child { border-bottom: 0; }
        .fm-upload-row .fm-upload-name { flex: 1 1 auto; min-width: 0; font-size: .82rem; }
        .fm-upload-row .fm-upload-name span { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .fm-upload-row .progress { height: 6px; width: 130px; }
        .fm-upload-row .fm-upload-status { width: 90px; font-size: .72rem; text-align: right; }

        /* Color swatches */
        .fm-color-swatch { width: 26px; height: 26px; border-radius: 50%; display: inline-block; cursor: pointer; border: 2px solid transparent; }
        .fm-color-swatch.selected { border-color: var(--bs-body-color, #495057); }

        .fm-card-fav, .fm-card-menu-btn { border: 0; background: transparent; padding: .15rem .3rem; }
        .fm-drop-zone-hint { border: 2px dashed var(--bs-border-color,#d9dee3); border-radius: var(--fm-radius); padding: 2.5rem 1rem; text-align: center; }
    </style>
@endsection

@php
    $categoryTiles = [
        'pdf' => ['bg' => '#ea5455', 'label' => 'PDF', 'icon' => null],
        'word' => ['bg' => '#396cd8', 'label' => 'DOC', 'icon' => null],
        'excel' => ['bg' => '#28c76f', 'label' => 'XLS', 'icon' => null],
        'powerpoint' => ['bg' => '#ff9f43', 'label' => 'PPT', 'icon' => null],
        'archive' => ['bg' => '#5e5873', 'label' => 'ZIP', 'icon' => null],
        'text' => ['bg' => '#82868b', 'label' => null, 'icon' => 'ti-file-text'],
        'video' => ['bg' => '#7367f0', 'label' => null, 'icon' => 'ti-video'],
        'audio' => ['bg' => '#00cfe8', 'label' => null, 'icon' => 'ti-music'],
        'image' => ['bg' => '#00cfe8', 'label' => null, 'icon' => 'ti-photo'],
        'other' => ['bg' => '#82868b', 'label' => null, 'icon' => 'ti-file'],
    ];
@endphp

@section('content')
<div class="container-fluid flex-grow-1 container-p-y fm-app" id="fm-app"
    data-can-create-folder="{{ $flags['canCreateFolder'] ? 1 : 0 }}"
    data-can-upload="{{ $flags['canUpload'] ? 1 : 0 }}"
    data-can-download="{{ $flags['canDownload'] ? 1 : 0 }}"
    data-can-preview="{{ $flags['canPreview'] ? 1 : 0 }}"
    data-can-rename="{{ $flags['canRename'] ? 1 : 0 }}"
    data-can-move="{{ $flags['canMove'] ? 1 : 0 }}"
    data-can-delete="{{ $flags['canDelete'] ? 1 : 0 }}"
    data-can-manage-all="{{ $flags['canManageAll'] ? 1 : 0 }}"
>

    {{-- Storage widget --}}
    <div class="card fm-storage-card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary rounded-pill p-2"><i class="ti ti-cloud ti-sm"></i></span>
                    <div>
                        <div class="fw-semibold" id="fm-storage-headline">{{ $stats['used_human'] }} used of {{ $stats['allocated_human'] }}</div>
                        <small class="text-muted"><span id="fm-stat-files">{{ $stats['total_files'] }}</span> files &bull; <span id="fm-stat-folders">{{ $stats['total_folders'] }}</span> folders</small>
                    </div>
                </div>
                <div class="flex-grow-1" style="min-width:220px;max-width:420px;">
                    <div class="fm-storage-bar {{ $stats['usage_percent'] >= 90 ? 'fm-storage-danger' : ($stats['usage_percent'] >= 70 ? 'fm-storage-warn' : '') }}" id="fm-storage-bar">
                        <div class="fm-storage-fill" id="fm-storage-fill" style="width: {{ min(100, $stats['usage_percent']) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted" id="fm-storage-percent">{{ $stats['usage_percent'] }}%</small>
                        <small class="text-muted"><span id="fm-stat-available">{{ $stats['available_human'] }}</span> available</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="fm-new-btn"
                    @if(!$flags['canCreateFolder'] && !$flags['canUpload']) disabled @endif>
                    <i class="ti ti-plus me-1 ti-xs"></i>New
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="fm-new-btn">
                    @if ($flags['canCreateFolder'])
                        <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#fmNewFolderModal"><i class="ti ti-folder-plus me-2"></i>New Folder</a></li>
                    @endif
                    @if ($flags['canUpload'])
                        <li><a class="dropdown-item" href="javascript:void(0);" id="fm-new-upload-trigger"><i class="ti ti-upload me-2"></i>Upload Files</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="fm-body">
        {{-- Sidebar --}}
        <div class="fm-sidebar-backdrop" id="fm-sidebar-backdrop"></div>
        <aside class="fm-sidebar card" id="fm-sidebar">
            <a href="javascript:void(0);" class="fm-nav-link active" data-context="folder" data-parent="">
                <i class="ti ti-folder ti-sm"></i> My Files
            </a>
            <a href="javascript:void(0);" class="fm-nav-link" data-context="recent">
                <i class="ti ti-clock ti-sm"></i> Recent
            </a>
            <a href="javascript:void(0);" class="fm-nav-link" data-context="favorites">
                <i class="ti ti-star ti-sm"></i> Favorites
            </a>
            @if ($flags['canDelete'])
                <a href="javascript:void(0);" class="fm-nav-link" data-context="trash">
                    <i class="ti ti-trash ti-sm"></i> Trash
                </a>
            @endif
            <hr class="my-2">
            <div class="d-flex align-items-center justify-content-between px-1 mb-1">
                <small class="text-uppercase text-muted fw-semibold" style="font-size:.68rem;">Folders</small>
            </div>
            <div id="fm-tree-root" data-parent-id=""></div>
        </aside>

        {{-- Main --}}
        <main class="fm-main">
            <div class="card">
                <div class="card-body">
                    <div class="fm-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-icon btn-outline-secondary d-lg-none" id="fm-sidebar-toggle"><i class="ti ti-menu-2"></i></button>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb fm-breadcrumb mb-0" id="fm-breadcrumb">
                                    <li class="breadcrumb-item active"><a class="fm-crumb" data-id="">My Files</a></li>
                                </ol>
                            </nav>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text"><i class="ti ti-search ti-xs"></i></span>
                                <input type="text" id="fm-search" class="form-control" placeholder="Search files & folders">
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#fmFilterModal">
                                <i class="ti ti-filter ti-xs me-0 me-sm-1"></i><span class="d-none d-sm-inline">Filter</span>
                                <span class="fm-filter-indicator d-none position-absolute top-0 end-0 translate-middle p-1 bg-warning border rounded-circle"></span>
                            </button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ti ti-arrows-sort ti-xs me-0 me-sm-1"></i><span class="d-none d-sm-inline">Sort</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end fm-sort-menu">
                                    <li><a class="dropdown-item fm-sort-option active" data-sort="name_asc" href="javascript:void(0);">Name A-Z</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="name_desc" href="javascript:void(0);">Name Z-A</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="newest" href="javascript:void(0);">Newest</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="oldest" href="javascript:void(0);">Oldest</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="modified" href="javascript:void(0);">Recently Modified</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="largest" href="javascript:void(0);">Largest</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="smallest" href="javascript:void(0);">Smallest</a></li>
                                    <li><a class="dropdown-item fm-sort-option" data-sort="type" href="javascript:void(0);">Type</a></li>
                                </ul>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary active" id="fm-view-grid-btn" title="Grid view"><i class="ti ti-layout-grid ti-xs"></i></button>
                                <button type="button" class="btn btn-outline-secondary" id="fm-view-list-btn" title="List view"><i class="ti ti-list ti-xs"></i></button>
                            </div>
                            @if ($flags['canUpload'])
                                <button type="button" class="btn btn-sm btn-outline-primary" id="fm-upload-btn"><i class="ti ti-upload ti-xs me-0 me-sm-1"></i><span class="d-none d-sm-inline">Upload</span></button>
                            @endif
                            @if ($flags['canCreateFolder'])
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fmNewFolderModal"><i class="ti ti-folder-plus ti-xs me-0 me-sm-1"></i><span class="d-none d-sm-inline">New Folder</span></button>
                            @endif
                        </div>
                    </div>

                    <div class="fm-quick-chips" id="fm-quick-chips">
                        <button type="button" class="fm-chip active" data-quick="">All</button>
                        <button type="button" class="fm-chip" data-quick="images"><i class="ti ti-photo ti-xs"></i>Images</button>
                        <button type="button" class="fm-chip" data-quick="documents"><i class="ti ti-file-text ti-xs"></i>Documents</button>
                        <button type="button" class="fm-chip" data-quick="pdfs"><i class="ti ti-file-type-pdf ti-xs"></i>PDFs</button>
                        <button type="button" class="fm-chip" data-quick="videos"><i class="ti ti-video ti-xs"></i>Videos</button>
                        <button type="button" class="fm-chip" data-quick="archives"><i class="ti ti-file-zip ti-xs"></i>Archives</button>
                        <button type="button" class="fm-chip" data-quick="large_files"><i class="ti ti-database ti-xs"></i>Large Files</button>
                    </div>

                    <div class="fm-selection-bar d-none" id="fm-selection-bar">
                        <strong><span id="fm-selection-count">0</span> item(s) selected</strong>
                        <div class="d-flex flex-wrap gap-2" id="fm-bulk-normal-actions">
                            @if ($flags['canMove'])
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-bulk-move"><i class="ti ti-arrows-move ti-xs me-1"></i>Move</button>
                            @endif
                            @if ($flags['canDownload'])
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-bulk-download"><i class="ti ti-download ti-xs me-1"></i>Download</button>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-bulk-favorite"><i class="ti ti-star ti-xs me-1"></i>Favorite</button>
                        </div>
                        @if ($flags['canDelete'])
                            <div class="d-flex flex-wrap gap-2 d-none" id="fm-bulk-trash-actions">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-bulk-restore"><i class="ti ti-rotate-clockwise-2 ti-xs me-1"></i>Restore</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="fm-bulk-force-delete"><i class="ti ti-trash-x ti-xs me-1"></i>Delete Permanently</button>
                            </div>
                        @endif
                        <button type="button" class="btn btn-sm btn-link ms-auto" id="fm-selection-clear">Clear</button>
                    </div>

                    <div id="fm-content">
                        <div class="fm-grid" id="fm-grid"></div>
                        <div class="fm-empty-state d-none" id="fm-empty-state">
                            <i class="ti ti-folder-open d-block mb-2"></i>
                            <h6 id="fm-empty-title">Your File Manager is empty</h6>
                            <p class="mb-3" id="fm-empty-subtitle">Create a folder or upload your first files.</p>
                            <div id="fm-empty-actions">
                                @if ($flags['canCreateFolder'])
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#fmNewFolderModal">New Folder</button>
                                @endif
                                @if ($flags['canUpload'])
                                    <button type="button" class="btn btn-sm btn-primary fm-empty-upload-btn">Upload Files</button>
                                @endif
                            </div>
                        </div>
                        <div class="text-center mt-3 d-none" id="fm-load-more-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-load-more">Load more</button>
                        </div>
                        <div class="text-center py-4 d-none" id="fm-loading"><span class="spinner-border spinner-border-sm text-primary"></span></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Drag & drop full-page overlay --}}
<div id="fm-drag-overlay" class="d-none">
    <div class="fm-drop-badge">
        <i class="ti ti-cloud-upload ti-lg text-primary mb-2 d-block"></i>
        <h5 class="mb-0">Drop files here to upload</h5>
    </div>
</div>

{{-- New Folder Modal --}}
<div class="modal fade" id="fmNewFolderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fmNewFolderForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Folder Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="fm-folder-name" class="form-control" required maxlength="255" autofocus>
                    </div>
                    <div class="mb-2">
                        <label class="form-label d-block">Color</label>
                        <div id="fm-color-picker">
                            @foreach (['#ffc107'=>'Amber','#7367f0'=>'Violet','#28c76f'=>'Green','#00cfe8'=>'Cyan','#ea5455'=>'Red','#ff9f43'=>'Orange','#82868b'=>'Grey','#396cd8'=>'Blue'] as $hex => $label)
                                <span class="fm-color-swatch {{ $loop->first ? 'selected' : '' }}" style="background: {{ $hex }};" data-color="{{ $hex }}" title="{{ $label }}"></span>
                            @endforeach
                        </div>
                        <input type="hidden" name="color" id="fm-folder-color" value="#ffc107">
                    </div>
                    @if ($flags['canManageAll'])
                        <div class="mb-1">
                            <label class="form-label">Owner</label>
                            <select name="owner_id" id="fm-folder-owner" class="form-select selectpicker" data-width="100%" title="Same as current folder / me">
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Upload Modal --}}
<div class="modal fade" id="fmUploadModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="fm-drop-zone-hint mb-3" id="fm-modal-dropzone">
                    <i class="ti ti-cloud-upload ti-lg mb-2 d-block text-primary"></i>
                    <p class="mb-2">Drag &amp; drop files here, or</p>
                    <input type="file" id="fm-upload-input" multiple class="form-control">
                    <small class="text-muted d-block mt-2">Max {{ (int) ceil($maxUploadSize / 1048576) }} MB per file</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1 d-none" id="fm-upload-summary-wrap">
                    <small class="text-muted" id="fm-upload-summary">0 files</small>
                    <small class="text-muted" id="fm-upload-overall-percent">0%</small>
                </div>
                <div class="progress mb-2 d-none" style="height:6px;" id="fm-upload-overall-wrap">
                    <div class="progress-bar" id="fm-upload-overall-progress" style="width:0%"></div>
                </div>
                <div id="fm-upload-list" style="max-height: 260px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Rename Modal --}}
<div class="modal fade" id="fmRenameModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fmRenameForm">
                @csrf
                <input type="hidden" name="id" id="fm-rename-id">
                <div class="modal-body">
                    <label class="form-label">New Name</label>
                    <input type="text" name="name" id="fm-rename-name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Rename</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Move Modal --}}
<div class="modal fade" id="fmMoveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Move To&hellip;</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <nav><ol class="breadcrumb fm-move-breadcrumb" id="fm-move-breadcrumb"></ol></nav>
                <div class="list-group" id="fm-move-folder-list" style="max-height: 260px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="fm-move-confirm">Move Here</button>
            </div>
        </div>
    </div>
</div>

{{-- Preview Drawer --}}
<div class="offcanvas offcanvas-end offcanvas-size-xl" tabindex="-1" id="fmPreviewDrawer">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title text-truncate" id="fm-preview-title">Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="flex-grow-1 d-flex align-items-center justify-content-center bg-light" id="fm-preview-body" style="min-height: 260px; overflow: auto;"></div>
        <div class="p-3 border-top">
            <div class="row small text-muted mb-2">
                <div class="col-6">Size: <span id="fm-preview-size" class="text-body"></span></div>
                <div class="col-6">Owner: <span id="fm-preview-owner" class="text-body"></span></div>
                <div class="col-6">Modified: <span id="fm-preview-modified" class="text-body"></span></div>
                <div class="col-6">Type: <span id="fm-preview-type" class="text-body"></span></div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="#" id="fm-preview-download" class="btn btn-sm btn-primary"><i class="ti ti-download ti-xs me-1"></i>Download</a>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-preview-rename"><i class="ti ti-edit ti-xs me-1"></i>Rename</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fm-preview-move"><i class="ti ti-arrows-move ti-xs me-1"></i>Move</button>
                <!-- <button type="button" class="btn btn-sm btn-outline-danger" id="fm-preview-delete"><i class="ti ti-trash ti-xs me-1"></i>Delete</button> -->
            </div>
        </div>
    </div>
</div>

{{-- Properties Modal --}}
<div class="modal fade" id="fmPropertiesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Properties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fm-properties-body"></div>
        </div>
    </div>
</div>

{{-- Filter Modal (Leads filter pattern: modal-lg, static backdrop, 3-col grid, bootstrap-select, daterangepicker) --}}
<div class="modal fade" id="fmFilterModal" aria-hidden="true" aria-labelledby="fmFilterModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="offcanvas-title" id="fmFilterModalLabel">File Manager Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <select id="fm-f-type" class="selectpicker w-100" data-style="default-btn" title="Type (All)">
                            <option value="">All</option>
                            <option value="file" {{ optional($savedFilter)->by_type === 'file' ? 'selected' : '' }}>Files</option>
                            <option value="folder" {{ optional($savedFilter)->by_type === 'folder' ? 'selected' : '' }}>Folders</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <select id="fm-f-category" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="File Category">
                            @foreach (['images'=>'Images','documents'=>'Documents','pdfs'=>'PDFs','spreadsheets'=>'Spreadsheets','presentations'=>'Presentations','videos'=>'Videos','audio'=>'Audio','archives'=>'Archives','other'=>'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ in_array($val, optional($savedFilter)->by_category_array ?? []) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <select id="fm-f-owner" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Owner">
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" {{ in_array((string) $owner->id, optional($savedFilter)->by_owner_array ?? []) ? 'selected' : '' }}>{{ $owner->name }} ({{ (int) $owner->user_type === 1 ? 'Admin' : 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" id="fm-f-date" class="form-control bsdatpicket" placeholder="Modified Date Range&hellip;"
                            value="{{ optional($savedFilter)->by_date_from && optional($savedFilter)->by_date_to ? $savedFilter->by_date_from.' - '.$savedFilter->by_date_to : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="number" min="0" id="fm-f-size-min" class="form-control" placeholder="Min Size (MB)" value="{{ optional($savedFilter)->by_size_min ? round($savedFilter->by_size_min/1048576) : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="number" min="0" id="fm-f-size-max" class="form-control" placeholder="Max Size (MB)" value="{{ optional($savedFilter)->by_size_max ? round($savedFilter->by_size_max/1048576) : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <select id="fm-f-location" class="selectpicker w-100" data-style="default-btn" title="Location">
                            <option value="current" {{ (optional($savedFilter)->by_location ?? 'current') === 'current' ? 'selected' : '' }}>Current Folder</option>
                            <option value="all" {{ optional($savedFilter)->by_location === 'all' ? 'selected' : '' }}>All Folders</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-warning btn-sm fm-reset-filter">Reset Filter</button>
                <button class="btn btn-success btn-sm fm-apply-filter">Apply &amp; Save Filter</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/sortablejs/sortable.js') }}"></script>
    <script>
    (function () {
        const $app = $('#fm-app');
        const flags = {
            canCreateFolder: $app.data('can-create-folder') == 1,
            canUpload: $app.data('can-upload') == 1,
            canDownload: $app.data('can-download') == 1,
            canPreview: $app.data('can-preview') == 1,
            canRename: $app.data('can-rename') == 1,
            canMove: $app.data('can-move') == 1,
            canDelete: $app.data('can-delete') == 1,
            canManageAll: $app.data('can-manage-all') == 1,
        };

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

        const tiles = @json($categoryTiles);

        // Single source of truth for "will the server actually stream this
        // inline" - kept in sync with config('file-manager.previewable_extensions')
        // instead of a second hand-maintained list drifting out of sync with it.
        const previewableExtensions = @json(config('file-manager.previewable_extensions'));
        const PREVIEW_RENDERERS = {
            image: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'],
            pdf: ['pdf'],
            video: ['mp4', 'webm'],
            audio: ['mp3', 'wav', 'ogg'],
            text: ['txt', 'csv'],
        };
        function previewRendererFor(ext) {
            for (const key in PREVIEW_RENDERERS) {
                if (PREVIEW_RENDERERS[key].includes(ext)) return key;
            }
            return null;
        }

        const routes = {
            browse: "{{ route('admin.file_manager.browse') }}",
            stats: "{{ route('admin.file_manager.stats') }}",
            tree: "{{ url('admin/file-manager/tree') }}",
            breadcrumb: "{{ url('admin/file-manager/breadcrumb') }}",
            folderCreate: "{{ route('admin.file_manager.folder.create') }}",
            upload: "{{ route('admin.file_manager.upload') }}",
            rename: "{{ route('admin.file_manager.rename') }}",
            move: "{{ route('admin.file_manager.move') }}",
            bulkMove: "{{ route('admin.file_manager.bulk_move') }}",
            favorite: "{{ route('admin.file_manager.favorite') }}",
            bulkFavorite: "{{ route('admin.file_manager.bulk_favorite') }}",
            delete: "{{ route('admin.file_manager.delete') }}",
            bulkDelete: "{{ route('admin.file_manager.bulk_delete') }}",
            restore: "{{ route('admin.file_manager.restore') }}",
            bulkRestore: "{{ route('admin.file_manager.bulk_restore') }}",
            forceDelete: "{{ route('admin.file_manager.force_delete') }}",
            bulkForceDelete: "{{ route('admin.file_manager.bulk_force_delete') }}",
            filterSave: "{{ route('admin.file_manager.filter.save') }}",
            filterReset: "{{ route('admin.file_manager.filter.reset') }}",
            download: function (id) { return "{{ url('admin/file-manager') }}/" + id + '/download'; },
            preview: function (id) { return "{{ url('admin/file-manager') }}/" + id + '/preview'; },
            thumbnail: function (id) { return "{{ url('admin/file-manager') }}/" + id + '/thumbnail'; },
        };

        const state = {
            context: 'folder',
            parentId: null,
            breadcrumbTrail: [],
            view: localStorage.getItem('fm_view') || 'grid',
            sort: localStorage.getItem('fm_sort') || 'name_asc',
            quick: '',
            search: '',
            page: 1,
            lastMeta: null,
            selection: new Map(), // id -> item data
            lastClickedId: null,
            renderedOrder: [],
        };

        applyViewClass();

        function applyViewClass() {
            $('#fm-content').toggleClass('fm-list', state.view === 'list');
            $('#fm-view-grid-btn').toggleClass('active', state.view === 'grid');
            $('#fm-view-list-btn').toggleClass('active', state.view === 'list');
        }

        $('.selectpicker').selectpicker();
        $('body').on('shown.bs.modal', '.modal', function () {
            $(this).find('.selectpicker').each(function () { $(this).selectpicker(); });
        });

        $('.bsdatpicket').daterangepicker({
            autoUpdateInput: false, opens: 'right', locale: { cancelLabel: 'Clear' },
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function () { $(this).val(''); });

        /* ---------------- Helpers ---------------- */

        function escapeHtml(str) {
            return $('<div>').text(str == null ? '' : str).html();
        }

        function updateFilterIndicator() {
            const has = $('#fm-f-type').val() || ($('#fm-f-category').val() || []).length || ($('#fm-f-owner').val() || []).length
                || $('#fm-f-date').val() || $('#fm-f-size-min').val() || $('#fm-f-size-max').val() || ($('#fm-f-location').val() && $('#fm-f-location').val() !== 'current');
            $('.fm-filter-indicator').toggleClass('d-none', !has);
        }

        function currentFilterPayload() {
            const dateVal = $('#fm-f-date').val();
            let dateFrom = '', dateTo = '';
            if (dateVal && dateVal.includes(' - ')) { [dateFrom, dateTo] = dateVal.split(' - '); }

            return {
                type: $('#fm-f-type').val() || '',
                category: $('#fm-f-category').val() || [],
                owner_id: $('#fm-f-owner').val() || [],
                date_from: dateFrom, date_to: dateTo,
                size_from: $('#fm-f-size-min').val() ? Math.round(parseFloat($('#fm-f-size-min').val()) * 1024 * 1024) : '',
                size_to: $('#fm-f-size-max').val() ? Math.round(parseFloat($('#fm-f-size-max').val()) * 1024 * 1024) : '',
                location: $('#fm-f-location').val() || 'current',
            };
        }

        const CATEGORY_QUICK_MAP = { images: 'images', documents: 'documents', pdfs: 'pdfs', spreadsheets: 'documents', presentations: 'documents', videos: 'videos', audio: 'audio', archives: 'archives' };

        function iconTileHtml(item) {
            const t = tiles[item.category] || tiles.other;
            const inner = t.label ? t.label : '<i class="ti ' + t.icon + '"></i>';
            return { bg: t.bg, html: inner };
        }

        function tileHtml(item) {
            if (item.type === 'folder') {
                const color = item.color || '#ffc107';
                return '<div class="fm-icon-tile" style="background:' + color + '22;"><i class="ti ti-folder-filled" style="color:' + color + ';font-size:2.1rem;"></i></div>';
            }
            if (item.has_thumbnail) {
                return '<div class="fm-icon-tile" style="background:rgba(var(--bs-light-rgb),1);"><img class="fm-card-thumb" src="' + routes.thumbnail(item.id) + '" loading="lazy"></div>';
            }
            const icon = iconTileHtml(item);
            return '<div class="fm-icon-tile" style="background:' + icon.bg + ';">' + icon.html + '</div>';
        }
        // A thumbnail can fail after has_thumbnail said yes (corrupt file, render
        // timeout, trashed item's route binding 404s) - the caller falls back to
        // the same category icon used when there's no thumbnail at all, instead
        // of a misleading hardcoded photo icon. Bound directly per-card (see
        // loadItems()) rather than delegated, since img "error" doesn't bubble.

        function cardHtml(item) {
            const selected = state.selection.has(String(item.id));
            const isTrash = state.context === 'trash';
            const menu = buildMenu(item);

            return '' +
            '<div class="fm-card" draggable="true" data-id="' + item.id + '" data-type="' + item.type + '" data-name="' + escapeHtml(item.name) + '">' +
                '<input type="checkbox" class="form-check-input fm-card-check" ' + (selected ? 'checked' : '') + '>' +
                (flags.canDelete && !isTrash ? '<button type="button" class="fm-card-fav ' + (item.favorite ? 'fm-active' : '') + '" title="Favorite"><i class="ti ti-star ti-sm"></i></button>' : '') +
                '<div class="dropdown">' +
                    '<button type="button" class="btn fm-card-menu-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm"></i></button>' +
                    '<div class="dropdown-menu dropdown-menu-end">' + menu + '</div>' +
                '</div>' +
                '<div class="fm-card-open">' +
                    tileHtml(item) +
                    '<div class="fm-card-body-list">' +
                        '<div class="fm-card-name" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</div>' +
                        '<div class="fm-card-meta">' + (item.type === 'folder' ? (item.child_count + ' item' + (item.child_count == 1 ? '' : 's')) : item.size_human) + '</div>' +
                        '<div class="fm-card-meta">' + (isTrash ? 'Deleted ' + item.deleted_at : item.modified_diff) + (flags.canManageAll ? ' &bull; ' + (item.owner ? item.owner.name : '') : '') + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        function buildMenu(item) {
            const isTrash = state.context === 'trash';
            let html = '';
            if (isTrash) {
                html += '<a class="dropdown-item fm-act-restore" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-rotate-clockwise-2 me-2"></i>Restore</a>';
                if (item.can_force_delete) {
                    html += '<a class="dropdown-item text-danger fm-act-force-delete" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-trash-x me-2"></i>Delete Permanently</a>';
                }
                return html;
            }
            if (item.type === 'folder') {
                html += '<a class="dropdown-item fm-act-open" href="javascript:void(0);" data-id="' + item.id + '" data-name="' + escapeHtml(item.name) + '"><i class="ti ti-folder-open me-2"></i>Open</a>';
            } else {
                if (flags.canPreview) html += '<a class="dropdown-item fm-act-preview" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-eye me-2"></i>Preview</a>';
                if (flags.canDownload) html += '<a class="dropdown-item" href="' + routes.download(item.id) + '"><i class="ti ti-download me-2"></i>Download</a>';
            }
            if (item.can_manage && flags.canRename) html += '<a class="dropdown-item fm-act-rename" href="javascript:void(0);" data-id="' + item.id + '" data-name="' + escapeHtml(item.name) + '"><i class="ti ti-edit me-2"></i>Rename</a>';
            if (item.can_manage && flags.canMove) html += '<a class="dropdown-item fm-act-move" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-arrows-move me-2"></i>Move</a>';
            html += '<a class="dropdown-item fm-act-favorite" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-star me-2"></i>' + (item.favorite ? 'Unfavorite' : 'Favorite') + '</a>';
            html += '<a class="dropdown-item fm-act-properties" href="javascript:void(0);" data-id="' + item.id + '"><i class="ti ti-info-circle me-2"></i>Properties</a>';
            if (item.can_manage && flags.canDelete) html += '<a class="dropdown-item text-danger fm-act-delete" href="javascript:void(0);" data-id="' + item.id + '" data-type="' + item.type + '"><i class="ti ti-trash me-2"></i>Delete</a>';
            return html;
        }

        let itemsById = {};

        function loadItems(reset) {
            if (reset) {
                state.page = 1;
                itemsById = {};
                $('#fm-grid').empty();
                clearSelection();
            }

            $('#fm-loading').removeClass('d-none');

            const payload = {
                context: state.context,
                parent_id: state.parentId,
                sort: state.sort,
                page: state.page,
                per_page: 40,
            };
            if (state.quick) payload.quick = state.quick;
            if (state.search) { payload.context = 'search'; payload.q = state.search; }

            if (state.context === 'folder' && !state.search) {
                const f = currentFilterPayload();
                if (f.type) payload.type = f.type;
                if (f.category.length === 1) payload.quick = CATEGORY_QUICK_MAP[f.category[0]] || payload.quick;
                if (f.owner_id.length === 1) payload.owner_id = f.owner_id[0];
                if (f.date_from) payload.date_from = f.date_from;
                if (f.date_to) payload.date_to = f.date_to;
                if (f.size_from) payload.size_from = f.size_from;
                if (f.size_to) payload.size_to = f.size_to;
                if (f.location) payload.location = f.location;
            }

            $.get(routes.browse, payload).done(function (res) {
                state.lastMeta = res.meta;
                res.data.forEach(function (item) {
                    itemsById[item.id] = item;
                    const $card = $(cardHtml(item));
                    // img/video/audio "error" events don't bubble, so a delegated
                    // $(document).on('error', ...) would never fire - bind directly
                    // to this card's own thumbnail element instead.
                    $card.find('.fm-card-thumb').on('error', function () {
                        const icon = iconTileHtml(item);
                        $(this).closest('.fm-icon-tile').css('background', icon.bg).html(icon.html);
                    });
                    $('#fm-grid').append($card);
                });
                renderEmptyState(res);
                $('#fm-load-more-wrap').toggleClass('d-none', res.meta.current_page >= res.meta.last_page);
            }).always(function () {
                $('#fm-loading').addClass('d-none');
            });
        }

        function renderEmptyState(res) {
            const isEmpty = $('#fm-grid').children().length === 0;
            $('#fm-empty-state').toggleClass('d-none', !isEmpty);
            if (!isEmpty) return;

            let title = 'Your File Manager is empty';
            let subtitle = 'Create a folder or upload your first files.';
            let showActions = true;

            if (state.search) {
                title = 'No files found';
                subtitle = 'Try a different search term.';
                showActions = false;
            } else if (state.quick || hasActiveFilters()) {
                title = 'No files match these filters';
                subtitle = 'Try adjusting or resetting your filters.';
                showActions = false;
            } else if (state.context === 'recent') {
                title = 'No recent files yet';
                subtitle = 'Files you open will show up here.';
                showActions = false;
            } else if (state.context === 'favorites') {
                title = 'No favorites yet';
                subtitle = 'Star files or folders to find them quickly.';
                showActions = false;
            } else if (state.context === 'trash') {
                title = 'Trash is empty';
                subtitle = 'Deleted files and folders will appear here.';
                showActions = false;
            }

            $('#fm-empty-title').text(title);
            $('#fm-empty-subtitle').text(subtitle);
            $('#fm-empty-actions').toggleClass('d-none', !showActions);
        }

        function hasActiveFilters() {
            const f = currentFilterPayload();
            return !!(f.type || f.category.length || f.owner_id.length || f.date_from || f.size_from || f.size_to || (f.location && f.location !== 'current'));
        }

        function reload() { loadItems(true); refreshStats(); }

        function refreshStats() {
            $.get(routes.stats).done(function (s) {
                $('#fm-stat-files').text(s.total_files);
                $('#fm-stat-folders').text(s.total_folders);
                $('#fm-stat-available').text(s.available_human);
                $('#fm-storage-headline').text(s.used_human + ' used of ' + s.allocated_human);
                $('#fm-storage-percent').text(s.usage_percent + '%');
                $('#fm-storage-fill').css('width', Math.min(100, s.usage_percent) + '%');
                $('#fm-storage-bar').removeClass('fm-storage-warn fm-storage-danger')
                    .addClass(s.usage_percent >= 90 ? 'fm-storage-danger' : (s.usage_percent >= 70 ? 'fm-storage-warn' : ''));
            });
        }

        $('#fm-load-more').on('click', function () { state.page++; loadItems(false); });

        /* ---------------- Left nav ---------------- */

        $(document).on('click', '.fm-nav-link[data-context]', function () {
            $('.fm-nav-link').removeClass('active');
            $(this).addClass('active');
            state.context = $(this).data('context');
            state.parentId = $(this).data('context') === 'folder' ? null : state.parentId;
            state.search = '';
            $('#fm-search').val('');
            if (state.context === 'folder') {
                state.breadcrumbTrail = [];
                renderBreadcrumb();
            } else {
                $('#fm-breadcrumb').html('<li class="breadcrumb-item active">' + $(this).text().trim() + '</li>');
            }
            closeMobileSidebar();
            reload();
        });

        function loadTree(parentId, $container) {
            $.get(routes.tree + '/' + (parentId || ''), {}).done(function (folders) {
                $container.empty();
                folders.forEach(function (f) {
                    const $item = $('' +
                        '<div class="fm-tree-item" data-id="' + f.id + '">' +
                            '<a href="javascript:void(0);" class="fm-nav-link fm-tree-nav" data-id="' + f.id + '" data-name="' + escapeHtml(f.name) + '">' +
                                '<span class="fm-tree-toggle" data-id="' + f.id + '">' + (f.has_children ? '<i class="ti ti-chevron-right ti-xs"></i>' : '') + '</span>' +
                                '<i class="ti ti-folder ti-sm" style="color:' + (f.color || '#ffc107') + ';"></i> ' +
                                '<span class="text-truncate">' + escapeHtml(f.name) + '</span>' +
                            '</a>' +
                            '<div class="fm-tree-children" data-id="' + f.id + '"></div>' +
                        '</div>'
                    );
                    $container.append($item);
                });
            });
        }
        loadTree(null, $('#fm-tree-root'));

        $(document).on('click', '.fm-tree-toggle', function (e) {
            e.stopPropagation();
            const id = $(this).data('id');
            const $children = $('.fm-tree-children[data-id="' + id + '"]');
            const expanded = $children.hasClass('expanded');
            $(this).find('i').toggleClass('ti-chevron-right', expanded).toggleClass('ti-chevron-down', !expanded);
            $children.toggleClass('expanded');
            if (!expanded && $children.is(':empty')) { loadTree(id, $children); }
        });

        $(document).on('click', '.fm-tree-nav', function () {
            $('.fm-nav-link').removeClass('active');
            $(this).addClass('active');
            state.context = 'folder';
            state.parentId = $(this).data('id');
            state.search = ''; $('#fm-search').val('');
            rebuildBreadcrumbToTreeNode($(this));
            closeMobileSidebar();
            reload();
        });

        function rebuildBreadcrumbToTreeNode($node) {
            $.get(routes.breadcrumb + '/' + state.parentId).done(function (res) {
                state.breadcrumbTrail = res.trail;
                renderBreadcrumb();
            });
        }

        $('#fm-sidebar-toggle').on('click', function () {
            $('#fm-sidebar').addClass('fm-open');
            $('#fm-sidebar-backdrop').addClass('fm-open');
        });
        function closeMobileSidebar() {
            $('#fm-sidebar').removeClass('fm-open');
            $('#fm-sidebar-backdrop').removeClass('fm-open');
        }
        $('#fm-sidebar-backdrop').on('click', closeMobileSidebar);

        /* ---------------- Breadcrumb / navigation ---------------- */

        function renderBreadcrumb() {
            const $bc = $('#fm-breadcrumb').empty();
            $bc.append('<li class="breadcrumb-item"><a class="fm-crumb" data-id="">My Files</a></li>');
            state.breadcrumbTrail.forEach(function (node, idx) {
                const last = idx === state.breadcrumbTrail.length - 1;
                $bc.append('<li class="breadcrumb-item' + (last ? ' active' : '') + '"><a class="fm-crumb" data-id="' + node.id + '">' + escapeHtml(node.name) + '</a></li>');
            });
        }

        function openFolder(id, name) {
            state.context = 'folder';
            state.parentId = id;
            state.search = ''; $('#fm-search').val('');
            if (id) {
                state.breadcrumbTrail.push({ id: id, name: name });
            } else {
                state.breadcrumbTrail = [];
            }
            renderBreadcrumb();
            $('.fm-nav-link').removeClass('active');
            $('.fm-nav-link[data-context="folder"]').addClass('active');
            reload();
        }

        $(document).on('click', '.fm-crumb', function () {
            const id = $(this).data('id');
            if (id === '' || id === undefined) {
                state.breadcrumbTrail = [];
                state.parentId = null;
            } else {
                const idx = state.breadcrumbTrail.findIndex(n => String(n.id) === String(id));
                state.breadcrumbTrail = state.breadcrumbTrail.slice(0, idx + 1);
                state.parentId = id;
            }
            renderBreadcrumb();
            reload();
        });

        /* ---------------- Card interactions ---------------- */

        $(document).on('dblclick', '.fm-card', function () {
            const id = $(this).data('id');
            const item = itemsById[id];
            if (!item) return;
            if (item.type === 'folder') { openFolder(item.id, item.name); }
            // Trash items only support Restore/Delete Permanently (see buildMenu()) -
            // Preview/Download routes 404 for a soft-deleted item's route binding, so
            // don't offer preview here either.
            else if (flags.canPreview && state.context !== 'trash') { openPreview(item); }
        });

        $(document).on('click', '.fm-card', function (e) {
            if ($(e.target).closest('.dropdown, .fm-card-fav').length) return;
            const id = String($(this).data('id'));
            const ids = $('#fm-grid .fm-card').map(function () { return String($(this).data('id')); }).get();

            if (e.shiftKey && state.lastClickedId) {
                const from = ids.indexOf(state.lastClickedId);
                const to = ids.indexOf(id);
                const [start, end] = from < to ? [from, to] : [to, from];
                for (let i = start; i <= end; i++) { selectItem(ids[i], true); }
            } else if (e.ctrlKey || e.metaKey) {
                if ($(e.target).hasClass('fm-card-check') || state.selection.has(id)) { toggleSelect(id); }
                else { selectItem(id, true); }
            } else if ($(e.target).hasClass('fm-card-check')) {
                toggleSelect(id);
            } else {
                clearSelection();
                selectItem(id, true);
            }
            state.lastClickedId = id;
            updateSelectionBar();
        });

        function selectItem(id, checked) {
            id = String(id);
            const $card = $('.fm-card[data-id="' + id + '"]');
            if (checked) {
                state.selection.set(id, itemsById[id]);
                $card.addClass('fm-selected').find('.fm-card-check').prop('checked', true);
            }
        }
        function toggleSelect(id) {
            id = String(id);
            const $card = $('.fm-card[data-id="' + id + '"]');
            if (state.selection.has(id)) {
                state.selection.delete(id);
                $card.removeClass('fm-selected').find('.fm-card-check').prop('checked', false);
            } else {
                selectItem(id, true);
            }
        }
        function clearSelection() {
            state.selection.clear();
            $('.fm-card').removeClass('fm-selected').find('.fm-card-check').prop('checked', false);
            updateSelectionBar();
        }
        function updateSelectionBar() {
            const n = state.selection.size;
            $('#fm-selection-bar').toggleClass('d-none', n === 0);
            $('#fm-selection-count').text(n);
            const isTrash = state.context === 'trash';
            $('#fm-bulk-normal-actions').toggleClass('d-none', isTrash);
            $('#fm-bulk-trash-actions').toggleClass('d-none', !isTrash);
        }
        $('#fm-selection-clear').on('click', clearSelection);

        $(document).on('click', '.fm-act-open', function () {
            openFolder($(this).data('id'), $(this).data('name'));
        });

        $(document).on('click', '.fm-card-fav, .fm-act-favorite', function (e) {
            e.preventDefault();
            const id = $(this).data('id') || $(this).closest('.fm-card').data('id');
            $.post(routes.favorite, { _token: '{{ csrf_token() }}', id: id }).done(function (res) {
                const item = itemsById[id];
                if (item) item.favorite = res.favorite;
                $('.fm-card[data-id="' + id + '"] .fm-card-fav').toggleClass('fm-active', res.favorite);
                if (state.context === 'favorites' && !res.favorite) { $('.fm-card[data-id="' + id + '"]').remove(); renderEmptyState({}); }
            });
        });

        /* ---------------- Search ---------------- */
        let searchTimer;
        $('#fm-search').on('keyup', function () {
            clearTimeout(searchTimer);
            const val = $(this).val().trim();
            searchTimer = setTimeout(function () {
                state.search = val;
                state.context = val ? 'search' : 'folder';
                reload();
            }, 400);
        });

        /* ---------------- Quick chips ---------------- */
        $(document).on('click', '.fm-chip', function () {
            $('.fm-chip').removeClass('active');
            $(this).addClass('active');
            state.quick = $(this).data('quick') || '';
            reload();
        });

        /* ---------------- Sort / View ---------------- */
        $(document).on('click', '.fm-sort-option', function () {
            $('.fm-sort-option').removeClass('active');
            $(this).addClass('active');
            state.sort = $(this).data('sort');
            localStorage.setItem('fm_sort', state.sort);
            reload();
        });
        $('#fm-view-grid-btn').on('click', function () { state.view = 'grid'; localStorage.setItem('fm_view', 'grid'); applyViewClass(); });
        $('#fm-view-list-btn').on('click', function () { state.view = 'list'; localStorage.setItem('fm_view', 'list'); applyViewClass(); });

        /* ---------------- Filter modal ---------------- */
        $('.fm-apply-filter').on('click', function () {
            const f = currentFilterPayload();
            $.post(routes.filterSave, {
                _token: '{{ csrf_token() }}',
                by_type: f.type, by_category: f.category, by_owner: f.owner_id,
                by_size_min: f.size_from, by_size_max: f.size_to,
                by_date_from: f.date_from, by_date_to: f.date_to, by_location: f.location,
            }).done(function (res) { toastr['success'](res.message, 'Filter'); });
            updateFilterIndicator();
            $('#fmFilterModal').modal('hide');
            reload();
        });
        $('.fm-reset-filter').on('click', function () {
            $('#fm-f-type, #fm-f-category, #fm-f-owner, #fm-f-location').selectpicker('deselectAll');
            $('#fm-f-location').selectpicker('val', 'current');
            $('#fm-f-date, #fm-f-size-min, #fm-f-size-max').val('');
            $.post(routes.filterReset, { _token: '{{ csrf_token() }}' }).done(function (res) { toastr['success'](res.message, 'Filter'); });
            updateFilterIndicator();
            reload();
        });
        updateFilterIndicator();

        /* ---------------- New Folder ---------------- */
        $('#fm-color-picker').on('click', '.fm-color-swatch', function () {
            $('.fm-color-swatch').removeClass('selected');
            $(this).addClass('selected');
            $('#fm-folder-color').val($(this).data('color'));
        });
        $('#fmNewFolderForm').on('submit', function (e) {
            e.preventDefault();
            $.post(routes.folderCreate, {
                _token: '{{ csrf_token() }}', name: $('#fm-folder-name').val(),
                parent_id: state.context === 'folder' ? state.parentId : null,
                color: $('#fm-folder-color').val(),
                owner_id: $('#fm-folder-owner').val() || null,
            }).done(function (res) {
                toastr['success'](res.message, 'Success');
                $('#fmNewFolderModal').modal('hide');
                $('#fm-folder-name').val('');
                reload();
            }).fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Failed to create folder.', 'Error'); });
        });

        /* ---------------- Upload (drag & drop + progress queue) ---------------- */
        let uploadQueue = [];
        let uploadActive = 0;
        const UPLOAD_CONCURRENCY = 3;

        function openUploadModal() { new bootstrap.Modal('#fmUploadModal').show(); }
        $('#fm-upload-btn, #fm-new-upload-trigger, .fm-empty-upload-btn').on('click', openUploadModal);

        $('#fm-upload-input').on('change', function () { queueFiles(this.files); this.value = ''; });

        const $modalDropzone = $('#fm-modal-dropzone');
        $modalDropzone.on('dragover', function (e) { e.preventDefault(); $(this).addClass('fm-dragover'); });
        $modalDropzone.on('dragleave drop', function () { $(this).removeClass('fm-dragover'); });
        $modalDropzone.on('drop', function (e) {
            e.preventDefault();
            queueFiles(e.originalEvent.dataTransfer.files);
        });

        let dragCounter = 0;
        $(window).on('dragenter', function (e) {
            if (!flags.canUpload) return;
            if (e.originalEvent.dataTransfer && Array.from(e.originalEvent.dataTransfer.types || []).includes('Files')) {
                dragCounter++;
                $('#fm-drag-overlay').removeClass('d-none');
            }
        });
        $(window).on('dragleave', function () {
            dragCounter = Math.max(0, dragCounter - 1);
            if (dragCounter === 0) $('#fm-drag-overlay').addClass('d-none');
        });
        $(window).on('dragover', function (e) { e.preventDefault(); });
        $(window).on('drop', function (e) {
            e.preventDefault();
            dragCounter = 0;
            $('#fm-drag-overlay').addClass('d-none');
            const files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
            if (files && files.length) { openUploadModal(); queueFiles(files); }
        });

        function queueFiles(fileList) {
            const files = Array.from(fileList);
            $('#fm-upload-summary-wrap, #fm-upload-overall-wrap').removeClass('d-none');
            files.forEach(function (file) {
                const rowId = 'fmu' + Math.random().toString(36).slice(2);
                uploadQueue.push({ id: rowId, file: file, status: 'waiting', progress: 0, xhr: null });
                $('#fm-upload-list').append(
                    '<div class="fm-upload-row" id="' + rowId + '">' +
                        '<i class="ti ti-file ti-sm text-muted"></i>' +
                        '<div class="fm-upload-name"><span>' + escapeHtml(file.name) + '</span></div>' +
                        '<div class="progress"><div class="progress-bar" style="width:0%"></div></div>' +
                        '<div class="fm-upload-status text-muted">waiting&hellip;</div>' +
                    '</div>'
                );
            });
            updateUploadSummary();
            processUploadQueue();
        }

        function updateUploadSummary() {
            const total = uploadQueue.length;
            const totalBytes = uploadQueue.reduce((s, u) => s + u.file.size, 0);
            $('#fm-upload-summary').text(total + ' file' + (total === 1 ? '' : 's') + ' • ' + humanBytes(totalBytes));
        }

        function humanBytes(bytes) {
            if (!bytes || bytes <= 0) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            const power = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
            return (bytes / Math.pow(1024, power)).toFixed(power === 0 ? 0 : 2) + ' ' + units[power];
        }

        function processUploadQueue() {
            const pending = uploadQueue.filter(u => u.status === 'waiting');
            while (uploadActive < UPLOAD_CONCURRENCY && pending.length) {
                const item = pending.shift();
                startUpload(item);
            }
        }

        function startUpload(item) {
            item.status = 'uploading';
            uploadActive++;
            const $row = $('#' + item.id);
            $row.find('.fm-upload-status').text('uploading');

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('parent_id', state.context === 'folder' ? (state.parentId || '') : '');
            fd.append('files[]', item.file);

            item.xhr = $.ajax({
                url: routes.upload, type: 'POST', data: fd, processData: false, contentType: false,
                xhr: function () {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            const pct = Math.round((evt.loaded / evt.total) * 100);
                            item.progress = pct;
                            $row.find('.progress-bar').css('width', pct + '%');
                            $row.find('.fm-upload-status').text(pct + '%');
                            updateOverallProgress();
                        }
                    });
                    return xhr;
                },
            }).done(function () {
                item.status = 'done'; item.progress = 100;
                $row.find('.progress-bar').removeClass('bg-danger').css('width', '100%');
                $row.find('.fm-upload-status').html('<i class="ti ti-check text-success"></i>');
                $row.find('i.ti-file').removeClass('text-muted').addClass('text-success');
            }).fail(function (xhr) {
                item.status = 'failed';
                $row.find('.progress-bar').addClass('bg-danger');
                const msg = xhr.responseJSON?.message || 'Failed';
                $row.find('.fm-upload-status').html('<a href="javascript:void(0);" class="fm-retry-upload text-danger" data-id="' + item.id + '" title="' + escapeHtml(msg) + '">Retry</a>');
            }).always(function () {
                uploadActive--;
                updateOverallProgress();
                processUploadQueue();
                if (uploadQueue.every(u => u.status === 'done' || u.status === 'failed')) {
                    reload();
                }
            });
        }

        function updateOverallProgress() {
            const total = uploadQueue.length || 1;
            const sum = uploadQueue.reduce((s, u) => s + (u.status === 'done' ? 100 : u.progress), 0);
            const pct = Math.round(sum / total);
            $('#fm-upload-overall-progress').css('width', pct + '%');
            $('#fm-upload-overall-percent').text(pct + '%');
        }

        $(document).on('click', '.fm-retry-upload', function () {
            const item = uploadQueue.find(u => u.id === $(this).data('id'));
            if (item) { item.status = 'waiting'; item.progress = 0; processUploadQueue(); }
        });

        $('#fmUploadModal').on('hidden.bs.modal', function () {
            uploadQueue.filter(u => u.status === 'uploading' || u.status === 'waiting').forEach(u => { if (u.xhr) u.xhr.abort(); });
            uploadQueue = [];
            $('#fm-upload-list').empty();
            $('#fm-upload-summary-wrap, #fm-upload-overall-wrap').addClass('d-none');
            $('#fm-upload-overall-progress').css('width', '0%');
        });

        /* ---------------- Rename ---------------- */
        $(document).on('click', '.fm-act-rename', function () {
            $('#fm-rename-id').val($(this).data('id'));
            $('#fm-rename-name').val($(this).data('name'));
            new bootstrap.Modal('#fmRenameModal').show();
        });
        $('#fmRenameForm').on('submit', function (e) {
            e.preventDefault();
            $.post(routes.rename, $(this).serialize()).done(function (res) {
                toastr['success'](res.message, 'Success');
                bootstrap.Modal.getInstance(document.getElementById('fmRenameModal')).hide();
                reload();
            }).fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Rename failed.', 'Error'); });
        });

        /* ---------------- Delete / Restore / Force Delete ---------------- */
        function deleteItem(id) {
            $.post(routes.delete, { _token: '{{ csrf_token() }}', id: id }).done(function (res) {
                if (res.status === 'confirm') {
                    if (confirm(res.message)) {
                        $.post(routes.delete, { _token: '{{ csrf_token() }}', id: id, force: 1 })
                            .done(function (r2) { toastr['success'](r2.message, 'Deleted'); reload(); })
                            .fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Delete failed.', 'Error'); });
                    }
                    return;
                }
                toastr['success'](res.message, 'Deleted');
                reload();
            }).fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Delete failed.', 'Error'); });
        }
        $(document).on('click', '.fm-act-delete', function () {
            if (!confirm('Delete this ' + $(this).data('type') + '?')) return;
            deleteItem($(this).data('id'));
        });
        $(document).on('click', '.fm-act-restore', function () {
            $.post(routes.restore, { _token: '{{ csrf_token() }}', id: $(this).data('id') })
                .done(function (res) { toastr['success'](res.message, 'Restored'); reload(); })
                .fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Restore failed.', 'Error'); });
        });
        $(document).on('click', '.fm-act-force-delete', function () {
            if (!confirm('Permanently delete this item? This cannot be undone.')) return;
            $.post(routes.forceDelete, { _token: '{{ csrf_token() }}', id: $(this).data('id') })
                .done(function (res) { toastr['success'](res.message, 'Deleted'); reload(); })
                .fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Delete failed.', 'Error'); });
        });

        /* ---------------- Bulk actions ---------------- */
        let bulkActionInFlight = false;

        function bulkButtons() { return $('#fm-selection-bar button'); }

        /**
         * Fires one aggregated bulk request (mirrors bulkMove's shape:
         * {status, <okKey>: [...ids], errors: [{id, message}]}), reports a
         * combined success/error toast, then refreshes the listing. Guards
         * against duplicate/overlapping requests via bulkActionInFlight.
         */
        function runBulk(url, ids, opts) {
            if (bulkActionInFlight || !ids.length) return;
            bulkActionInFlight = true;
            bulkButtons().prop('disabled', true);

            $.post(url, { _token: '{{ csrf_token() }}', ids: ids })
                .done(function (res) {
                    const ok = res[opts.okKey] || [];
                    const errors = res.errors || [];
                    errors.forEach(function (e) { toastr['error'](e.message, opts.errorTitle); });
                    if (ok.length) { toastr['success'](ok.length + ' ' + opts.successSuffix, 'Success'); }
                    clearSelection();
                    reload();
                })
                .fail(function (xhr) {
                    toastr['error'](xhr.responseJSON?.message || opts.failMessage, 'Error');
                })
                .always(function () {
                    bulkActionInFlight = false;
                    bulkButtons().prop('disabled', false);
                });
        }

        $('#fm-bulk-favorite').on('click', function () {
            const ids = Array.from(state.selection.keys());
            runBulk(routes.bulkFavorite, ids, { okKey: 'updated', successSuffix: 'item(s) added to Favorites.', errorTitle: 'Favorite failed', failMessage: 'Favorite failed.' });
        });
        $('#fm-bulk-restore').on('click', function () {
            const ids = Array.from(state.selection.keys());
            runBulk(routes.bulkRestore, ids, { okKey: 'restored', successSuffix: 'item(s) restored.', errorTitle: 'Restore failed', failMessage: 'Restore failed.' });
        });
        $('#fm-bulk-force-delete').on('click', function () {
            if (bulkActionInFlight) return;
            const ids = Array.from(state.selection.keys());
            if (!ids.length) return;
            if (!confirm('Permanently delete ' + ids.length + ' item(s)? This cannot be undone.')) return;
            runBulk(routes.bulkForceDelete, ids, { okKey: 'deleted', successSuffix: 'item(s) permanently deleted.', errorTitle: 'Delete failed', failMessage: 'Delete failed.' });
        });
        $('#fm-bulk-download').on('click', function () {
            if (bulkActionInFlight) return;
            const items = Array.from(state.selection.values()).filter(function (item) { return item && item.type === 'file'; });
            const skippedFolders = state.selection.size - items.length;

            if (!items.length) {
                toastr['info']('Select at least one file to download (folders can\'t be downloaded).', 'Nothing to download');
                return;
            }

            bulkActionInFlight = true;
            bulkButtons().prop('disabled', true);

            let blocked = 0;
            items.forEach(function (item, i) {
                setTimeout(function () {
                    const win = window.open(routes.download(item.id), '_blank');
                    if (!win) blocked++;
                    if (i === items.length - 1) {
                        bulkActionInFlight = false;
                        bulkButtons().prop('disabled', false);
                        if (blocked) {
                            toastr['warning']('Your browser blocked ' + blocked + ' download(s). Please allow pop-ups for this site.', 'Downloads blocked');
                        } else {
                            toastr['success'](items.length + ' download(s) started.' + (skippedFolders ? ' (' + skippedFolders + ' folder(s) skipped.)' : ''), 'Success');
                        }
                    }
                }, i * 250);
            });
        });

        /* ---------------- Move (single + bulk + drag&drop) ---------------- */
        let moveTargetIds = [];
        let moveBrowseParentId = null;
        let moveBrowseTrail = [];

        function openMoveModal(ids) {
            moveTargetIds = ids;
            moveBrowseParentId = state.context === 'folder' ? state.parentId : null;
            moveBrowseTrail = state.context === 'folder' ? state.breadcrumbTrail.slice() : [];
            loadMoveFolders();
            new bootstrap.Modal('#fmMoveModal').show();
        }
        $(document).on('click', '.fm-act-move', function () { openMoveModal([$(this).data('id')]); });
        $('#fm-bulk-move').on('click', function () { openMoveModal(Array.from(state.selection.keys())); });

        function loadMoveFolders() {
            const $bc = $('#fm-move-breadcrumb').empty();
            $bc.append('<li class="breadcrumb-item"><a class="fm-move-crumb" data-id="" href="javascript:void(0);"><i class="ti ti-home ti-xs"></i></a></li>');
            moveBrowseTrail.forEach(function (n) {
                $bc.append('<li class="breadcrumb-item"><a class="fm-move-crumb" data-id="' + n.id + '" href="javascript:void(0);">' + escapeHtml(n.name) + '</a></li>');
            });

            $.get(routes.browse, { context: 'folder', parent_id: moveBrowseParentId, type: 'folder', per_page: 60 }).done(function (res) {
                const $list = $('#fm-move-folder-list').empty();
                res.data.forEach(function (row) {
                    if (moveTargetIds.includes(String(row.id))) return;
                    $list.append('<a href="javascript:void(0);" class="list-group-item list-group-item-action fm-move-into" data-id="' + row.id + '"><i class="ti ti-folder text-warning me-2"></i>' + escapeHtml(row.name) + '</a>');
                });
            });
        }
        $(document).on('click', '.fm-move-crumb', function () {
            const id = $(this).data('id');
            if (id === '' || id === undefined) { moveBrowseParentId = null; moveBrowseTrail = []; }
            else {
                const idx = moveBrowseTrail.findIndex(n => String(n.id) === String(id));
                moveBrowseTrail = moveBrowseTrail.slice(0, idx + 1);
                moveBrowseParentId = id;
            }
            loadMoveFolders();
        });
        $(document).on('click', '.fm-move-into', function () {
            moveBrowseParentId = $(this).data('id');
            moveBrowseTrail.push({ id: moveBrowseParentId, name: $(this).text().trim() });
            loadMoveFolders();
        });
        $('#fm-move-confirm').on('click', function () {
            performMove(moveTargetIds, moveBrowseParentId, function () {
                bootstrap.Modal.getInstance(document.getElementById('fmMoveModal')).hide();
            });
        });

        let moveInFlight = false;
        function performMove(ids, parentId, onDone) {
            if (moveInFlight || !ids.length) return;
            moveInFlight = true;
            $('#fm-move-confirm').prop('disabled', true);

            $.post(routes.bulkMove, { _token: '{{ csrf_token() }}', ids: ids, parent_id: parentId })
                .done(function (res) {
                    if (res.errors && res.errors.length) {
                        res.errors.forEach(e => toastr['error'](e.message, 'Move failed'));
                    }
                    if (res.moved.length) { toastr['success'](res.moved.length + ' item(s) moved.', 'Success'); }
                    clearSelection();
                    reload();
                    if (onDone) onDone();
                })
                .fail(function (xhr) { toastr['error'](xhr.responseJSON?.message || 'Move failed.', 'Error'); })
                .always(function () {
                    moveInFlight = false;
                    $('#fm-move-confirm').prop('disabled', false);
                });
        }

        // Drag-to-move: cards are draggable; folders (cards / tree / breadcrumb) are drop targets.
        $(document).on('dragstart', '.fm-card', function (e) {
            const id = String($(this).data('id'));
            let ids = state.selection.has(id) ? Array.from(state.selection.keys()) : [id];
            e.originalEvent.dataTransfer.setData('text/fm-ids', JSON.stringify(ids));
            e.originalEvent.dataTransfer.effectAllowed = 'move';
            $(this).addClass('fm-dragging');
        });
        $(document).on('dragend', '.fm-card', function () { $(this).removeClass('fm-dragging'); });

        function bindDropTarget(selector, resolveTargetId) {
            $(document).on('dragover', selector, function (e) {
                const dt = e.originalEvent.dataTransfer;
                if (!dt || !Array.from(dt.types || []).includes('text/fm-ids')) return;
                e.preventDefault();
                $(this).addClass('fm-drop-hover');
            });
            $(document).on('dragleave', selector, function () { $(this).removeClass('fm-drop-hover'); });
            $(document).on('drop', selector, function (e) {
                e.preventDefault();
                $(this).removeClass('fm-drop-hover');
                const raw = e.originalEvent.dataTransfer.getData('text/fm-ids');
                if (!raw) return;
                const ids = JSON.parse(raw);
                const targetId = resolveTargetId.call(this);
                if (ids.includes(String(targetId))) return;
                performMove(ids, targetId);
            });
        }
        bindDropTarget('.fm-card[data-type="folder"]', function () { return $(this).data('id'); });
        bindDropTarget('.fm-tree-item > .fm-nav-link', function () { return $(this).data('id'); });
        bindDropTarget('.fm-nav-link[data-context="folder"]', function () { return null; });
        bindDropTarget('.fm-crumb', function () { return $(this).data('id') || null; });

        /* ---------------- Preview ---------------- */
        function previewLoadingHtml() {
            return '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div>' +
                '<p class="text-muted small mt-3 mb-0">Loading preview&hellip;</p></div>';
        }

        function previewFallbackHtml(item, message) {
            const icon = iconTileHtml(item);
            const downloadBtn = flags.canDownload
                ? '<a href="' + routes.download(item.id) + '" class="btn btn-sm btn-primary mt-3"><i class="ti ti-download ti-xs me-1"></i>Download</a>'
                : '';
            return '<div class="text-center py-5">' +
                '<div class="fm-icon-tile mx-auto mb-2" style="width:96px;height:96px;background:' + icon.bg + ';">' + icon.html + '</div>' +
                '<p class="text-muted mb-0">' + escapeHtml(message) + '</p>' +
                downloadBtn +
                '</div>';
        }

        // Renders the actual media tag and wires a direct (non-delegated) error
        // handler on it - img/video/audio "error" events don't bubble, so this
        // has to bind straight to the element, not via $(document).on(...).
        function renderPreviewMedia(item, renderer, requestToken) {
            const url = routes.preview(item.id);
            let $el;

            if (renderer === 'image') {
                $el = $('<img>').attr('src', url).css({ maxWidth: '100%', maxHeight: '60vh', objectFit: 'contain' });
            } else if (renderer === 'pdf' || renderer === 'text') {
                $el = $('<iframe>').attr('src', url).css({ width: '100%', height: '60vh', border: 0 });
            } else if (renderer === 'video') {
                $el = $('<video>').attr({ src: url, controls: true, preload: 'metadata' }).css({ maxWidth: '100%', maxHeight: '60vh' });
            } else if (renderer === 'audio') {
                $el = $('<audio>').attr({ src: url, controls: true, preload: 'metadata' }).css({ width: '100%', maxWidth: '420px' });
            }

            const showFallback = function () {
                if (requestToken !== previewRequestToken) return;
                $('#fm-preview-body').html(previewFallbackHtml(item, 'This file could not be loaded for preview.'));
            };

            if (renderer === 'video' || renderer === 'audio') {
                // <video>/<audio> fire 'error' on the element itself for load failures
                // (e.g. a codec the browser can't decode even though the file streamed
                // fine) - catch that as a backstop beyond the HEAD preflight, which
                // can't detect client-side codec support.
                $el.on('error', showFallback);
                const wrap = $('<div class="text-center py-4 w-100">');
                if (renderer === 'audio') {
                    const icon = iconTileHtml(item);
                    wrap.append('<div class="fm-icon-tile mx-auto mb-3" style="width:96px;height:96px;background:' + icon.bg + ';">' + icon.html + '</div>');
                }
                wrap.append($el);
                $('#fm-preview-body').html(wrap);
            } else if (renderer === 'image') {
                $el.on('error', showFallback);
                $('#fm-preview-body').html($el);
            } else {
                // iframes don't reliably fire a catchable error for HTTP-level
                // failures - the HEAD preflight is what actually guards this case.
                $('#fm-preview-body').html($el);
            }
        }

        let previewRequestToken = 0;

        function openPreview(item) {
            $('#fm-preview-title').text(item.name);
            $('#fm-preview-size').text(item.size_human || '-');
            $('#fm-preview-owner').text(item.owner ? item.owner.name : '-');
            $('#fm-preview-modified').text(item.modified || '-');
            $('#fm-preview-type').text((item.extension || '').toUpperCase() || '-');
            $('#fm-preview-download').attr('href', routes.download(item.id)).toggleClass('d-none', !flags.canDownload);
            $('#fm-preview-rename').data('id', item.id).data('name', item.name).toggleClass('d-none', !(item.can_manage && flags.canRename));
            $('#fm-preview-move').data('id', item.id).toggleClass('d-none', !(item.can_manage && flags.canMove));
            // $('#fm-preview-delete').data('id', item.id).data('type', item.type).toggleClass('d-none', !(item.can_manage && flags.canDelete));

            const myToken = ++previewRequestToken;
            const ext = (item.extension || '').toLowerCase();
            const renderer = previewableExtensions.includes(ext) ? previewRendererFor(ext) : null;

            new bootstrap.Offcanvas('#fmPreviewDrawer').show();

            if (!renderer) {
                if (item.has_thumbnail) {
                    // Excel etc: no inline viewer, but we already rendered a real grid
                    // thumbnail server-side for the listing - show it larger here too.
                    $('#fm-preview-body').html(
                        '<div class="text-center"><img src="' + routes.thumbnail(item.id) + '" style="max-width:100%;max-height:56vh;object-fit:contain;border:1px solid var(--bs-border-color,#d9dee3);border-radius:.4rem;">' +
                        '<p class="text-muted small mt-2 mb-0">Preview shows the first sheet only.</p></div>'
                    );
                } else {
                    $('#fm-preview-body').html(previewFallbackHtml(item, 'Preview not available for this file type.'));
                }
                return;
            }

            $('#fm-preview-body').html(previewLoadingHtml());

            // Confirm the file is actually reachable (still exists, actor still has
            // permission) via a cheap HEAD request before wiring up a renderer - the
            // same route/controller/policy check the real <img>/<video>/<iframe>
            // request will hit, so this can't drift out of sync with it. Without
            // this, a 403/404 just renders as a silently blank/broken element.
            $.ajax({ url: routes.preview(item.id), method: 'HEAD' })
                .done(function () {
                    if (myToken !== previewRequestToken) return;
                    renderPreviewMedia(item, renderer, myToken);
                })
                .fail(function (xhr) {
                    if (myToken !== previewRequestToken) return;
                    const message = xhr.status === 403
                        ? 'You do not have permission to preview this file.'
                        : xhr.status === 404
                            ? 'This file could not be found - it may have been moved or deleted.'
                            : 'This file could not be loaded for preview.';
                    $('#fm-preview-body').html(previewFallbackHtml(item, message));
                });
        }
        $(document).on('click', '.fm-act-preview', function () {
            const item = itemsById[$(this).data('id')];
            if (item) openPreview(item);
        });
        $('#fm-preview-rename').on('click', function () {
            $('#fm-rename-id').val($(this).data('id'));
            $('#fm-rename-name').val($(this).data('name'));
            new bootstrap.Modal('#fmRenameModal').show();
        });
        $('#fm-preview-move').on('click', function () { openMoveModal([String($(this).data('id'))]); });
        // $('#fm-preview-delete').on('click', function () {
        //     if (!confirm('Delete this ' + $(this).data('type') + '?')) return;
        //     deleteItem($(this).data('id'));
        //     bootstrap.Offcanvas.getInstance(document.getElementById('fmPreviewDrawer'))?.hide();
        // });

        /* ---------------- Properties ---------------- */
        $(document).on('click', '.fm-act-properties', function () {
            const item = itemsById[$(this).data('id')];
            if (!item) return;
            let html = '<table class="table table-borderless table-sm mb-0">';
            html += '<tr><th class="text-muted" style="width:130px;">Name</th><td>' + escapeHtml(item.name) + '</td></tr>';
            html += '<tr><th class="text-muted">Type</th><td>' + (item.type === 'folder' ? 'Folder' : (item.extension || '').toUpperCase()) + '</td></tr>';
            if (item.type === 'file') html += '<tr><th class="text-muted">Size</th><td>' + item.size_human + '</td></tr>';
            if (item.type === 'folder') html += '<tr><th class="text-muted">Items</th><td>' + item.child_count + '</td></tr>';
            html += '<tr><th class="text-muted">Owner</th><td>' + (item.owner ? item.owner.name : '-') + ' (' + item.owner_type + ')</td></tr>';
            html += '<tr><th class="text-muted">Modified</th><td>' + item.modified + '</td></tr>';
            html += '</table>';
            $('#fm-properties-body').html(html);
            new bootstrap.Modal('#fmPropertiesModal').show();
        });

        /* ---------------- Init ---------------- */
        loadItems(true);
    })();
    </script>
@endsection
