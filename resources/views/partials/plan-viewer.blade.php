{{--
    Reusable drawing viewer: renders page 1+ of an uploaded PDF onto a canvas
    via PDF.js, then overlays "professional markings" — pins (single point)
    and lines (start/end point) — each carrying a review comment.

    Markups are added/removed via fetch(), never a page reload: click the
    drawing, type the comment in the inline box that appears right there,
    hit Save — the marker appears immediately. Clicking an existing marker
    opens it with a Delete option, for correcting a mis-placed mark.

    Expected variables:
      $previewUrl  (string)      same-origin inline preview URL for the PDF
      $downloadUrl (string|null) same-origin download URL (attachment)
      $fileName    (string|null) original uploaded filename
      $hasDrawings (bool)
      $markups     (array)       [['id','type','page','x','y','x2','y2','comment','user_name','created_at'], ...]
      $interactive (bool)        true = council can add/delete pins/lines
      $storeUrl    (string|null) required when $interactive is true
      $viewerId    (string)      unique id, e.g. "council-12"
--}}
@php
    $isPdf = $fileName && str_ends_with(strtolower($fileName), '.pdf');
@endphp

<div class="plan-viewer-wrap">
    @if (! ($hasDrawings ?? false))
        <p class="text-muted mb-0">No drawings have been uploaded for this application.</p>
    @elseif (! $isPdf)
        <p class="text-muted">
            Preview isn't available for this file type ({{ $fileName }}).
            @if ($downloadUrl)
                <a href="{{ $downloadUrl }}" class="small-link">Download to view</a>.
            @endif
        </p>
    @else
        <div class="plan-viewer"
             id="planViewer-{{ $viewerId }}"
             data-pdf-url="{{ $previewUrl }}"
             data-store-url="{{ $storeUrl }}"
             data-interactive="{{ $interactive ? '1' : '0' }}"
             data-markups="{{ json_encode($markups ?? []) }}">

            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                @if ($interactive)
                    <div class="btn-group btn-group-sm plan-viewer-toolbar" role="group">
                        <button type="button" class="btn btn-outline-primary" data-tool="pin"><i class="ri-map-pin-line"></i> Add Pin</button>
                        <button type="button" class="btn btn-outline-primary" data-tool="line"><i class="ri-route-line"></i> Draw Line</button>
                        <button type="button" class="btn btn-outline-secondary" data-tool="none"><i class="ri-close-line"></i> Done</button>
                    </div>
                    <span class="small text-muted plan-viewer-hint">Select a tool, then click on the drawing. Click an existing mark to edit or delete it.</span>
                @endif
                @if ($downloadUrl)
                    <a href="{{ $downloadUrl }}" class="btn btn-outline-secondary btn-sm ms-auto">
                        <i class="ri-download-line me-1"></i> Download original
                    </a>
                @endif
            </div>

            <div class="plan-viewer-stage position-relative border rounded bg-light overflow-hidden">
                <div class="plan-viewer-loading position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading drawing…
                </div>
                <canvas class="plan-viewer-canvas d-block w-100"></canvas>
                <svg class="plan-viewer-overlay-svg position-absolute top-0 start-0 w-100 h-100"
                     viewBox="0 0 100 100" preserveAspectRatio="none"></svg>
                <div class="plan-viewer-pins position-absolute top-0 start-0 w-100 h-100"></div>

                @if ($interactive)
                    <div class="plan-viewer-popover plan-viewer-composer d-none position-absolute">
                        <textarea class="form-control form-control-sm" rows="2" placeholder="What needs correcting here?" maxlength="1000"></textarea>
                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-sm btn-primary plan-viewer-composer-save">
                                <span class="plan-viewer-composer-spinner spinner-border spinner-border-sm d-none"></span>
                                <span class="plan-viewer-composer-save-label">Save</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-light plan-viewer-composer-cancel">Cancel</button>
                        </div>
                    </div>

                    <div class="plan-viewer-popover plan-viewer-viewer d-none position-absolute">
                        <div class="plan-viewer-viewer-meta small text-muted mb-1"></div>
                        <div class="plan-viewer-viewer-comment small mb-2"></div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-danger plan-viewer-viewer-delete">
                                <span class="plan-viewer-viewer-spinner spinner-border spinner-border-sm d-none"></span>
                                <span class="plan-viewer-viewer-delete-label">Delete</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-light plan-viewer-viewer-close">Close</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                <div class="plan-viewer-pagenav d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary plan-viewer-prev">&larr;</button>
                    <span class="plan-viewer-pageinfo">Page 1</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary plan-viewer-next">&rarr;</button>
                </div>
            </div>

            <ol class="plan-viewer-legend mt-3 ps-3"></ol>
        </div>

        @once
            <style>
                .plan-viewer-stage { min-height: 300px; cursor: default; }
                .plan-viewer-stage.tool-active { cursor: crosshair; }
                .plan-viewer-loading { background: rgba(255,255,255,.7); font-size: .85rem; color: #6b7280; transition: opacity .15s ease; }
                .plan-viewer-loading.d-none { opacity: 0; }
                .plan-viewer-pins { pointer-events: none; }
                .plan-viewer-pin {
                    position: absolute;
                    width: 26px; height: 26px;
                    margin-left: -13px; margin-top: -13px;
                    border-radius: 50%;
                    background: #dc3545;
                    color: #fff;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 12px; font-weight: 700;
                    border: 2px solid #fff;
                    box-shadow: 0 1px 4px rgba(0,0,0,.4);
                    cursor: pointer;
                    pointer-events: auto;
                    animation: plan-viewer-pop .15s ease-out;
                }
                .plan-viewer-pin.is-pending { opacity: .6; }
                .plan-viewer-pin.is-dot { width: 10px; height: 10px; margin-left: -5px; margin-top: -5px; }
                .plan-viewer-pin.is-line-marker { background: #0d6efd; }
                @keyframes plan-viewer-pop {
                    from { transform: scale(.4); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }
                .plan-viewer-line {
                    stroke: #dc3545;
                    stroke-width: 0.6;
                    vector-effect: non-scaling-stroke;
                }
                .plan-viewer-line.is-rubber {
                    stroke-dasharray: 1.2 1;
                    stroke: #0d6efd;
                    pointer-events: none;
                }
                .plan-viewer-toolbar button.active { background: #0d6efd; color: #fff; border-color: #0d6efd; }
                .plan-viewer-legend li { margin-bottom: 6px; }
                .plan-viewer-legend .badge { margin-right: 6px; }
                .plan-viewer-popover {
                    width: 230px;
                    background: #fff;
                    border: 1px solid #d8dee8;
                    border-radius: 8px;
                    box-shadow: 0 6px 20px rgba(0,0,0,.15);
                    padding: 8px;
                    z-index: 5;
                    transform: translate(-50%, 10px);
                }
                .plan-viewer-composer textarea { resize: none; }
                .plan-viewer-viewer-comment { color: #1f2937; word-break: break-word; }
            </style>

            <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
            <script>
            (function () {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';

                function initViewer(root) {
                    const pdfUrl = root.dataset.pdfUrl;
                    const storeUrl = root.dataset.storeUrl;
                    const interactive = root.dataset.interactive === '1';
                    const markups = JSON.parse(root.dataset.markups || '[]');
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.content : '';

                    const stage = root.querySelector('.plan-viewer-stage');
                    const loading = root.querySelector('.plan-viewer-loading');
                    const canvas = root.querySelector('.plan-viewer-canvas');
                    const ctx = canvas.getContext('2d');
                    const svg = root.querySelector('.plan-viewer-overlay-svg');
                    const pinsLayer = root.querySelector('.plan-viewer-pins');
                    const pageInfo = root.querySelector('.plan-viewer-pageinfo');
                    const legend = root.querySelector('.plan-viewer-legend');
                    const hint = root.querySelector('.plan-viewer-hint');
                    const composer = root.querySelector('.plan-viewer-composer');
                    const composerText = composer ? composer.querySelector('textarea') : null;
                    const viewer = root.querySelector('.plan-viewer-viewer');
                    const viewerMeta = viewer ? viewer.querySelector('.plan-viewer-viewer-meta') : null;
                    const viewerComment = viewer ? viewer.querySelector('.plan-viewer-viewer-comment') : null;

                    let pdfDoc = null;
                    let currentPage = 1;
                    let totalPages = 1;
                    let tool = 'none';
                    let lineStart = null;
                    let lineStartDot = null;
                    let rubberLine = null;
                    let pending = null; // { data, els: [...] } awaiting a comment
                    let viewingMarkupId = null;

                    function escapeHtml(s) {
                        const div = document.createElement('div');
                        div.textContent = s;
                        return div.innerHTML;
                    }

                    function renderLegend() {
                        legend.innerHTML = '';
                        const onPage = markups.filter(m => (m.page || 1) === currentPage);
                        if (! onPage.length) {
                            legend.innerHTML = '<li class="text-muted" style="list-style:none;">No markings on this page yet.</li>';
                            return;
                        }
                        onPage.forEach(function (m, i) {
                            const li = document.createElement('li');
                            const kind = m.type === 'pin' ? 'Pin' : 'Line';
                            const badgeClass = m.type === 'pin' ? 'bg-danger' : 'bg-primary';
                            li.innerHTML = '<span class="badge ' + badgeClass + '">' + kind + ' ' + (i + 1) + '</span>' +
                                '<strong>' + escapeHtml(m.user_name || 'Reviewer') + '</strong>: ' +
                                (m.comment ? escapeHtml(m.comment) : '<span class="text-muted">no comment</span>');
                            legend.appendChild(li);
                        });
                    }

                    function drawMarkups() {
                        svg.innerHTML = '';
                        pinsLayer.innerHTML = '';
                        const onPage = markups.filter(m => (m.page || 1) === currentPage);

                        onPage.forEach(function (m, i) {
                            const label = String(i + 1);
                            const tooltip = (m.user_name || 'Reviewer') + ': ' + (m.comment || '');

                            if (m.type === 'pin') {
                                const pin = document.createElement('div');
                                pin.className = 'plan-viewer-pin';
                                pin.style.left = (m.x * 100) + '%';
                                pin.style.top = (m.y * 100) + '%';
                                pin.textContent = label;
                                pin.title = tooltip;
                                if (interactive) {
                                    pin.addEventListener('click', function (e) {
                                        e.stopPropagation();
                                        openViewer(m, m.x, m.y);
                                    });
                                }
                                pinsLayer.appendChild(pin);
                            } else if (m.type === 'line') {
                                const ns = 'http://www.w3.org/2000/svg';
                                const line = document.createElementNS(ns, 'line');
                                line.setAttribute('x1', m.x * 100);
                                line.setAttribute('y1', m.y * 100);
                                line.setAttribute('x2', m.x2 * 100);
                                line.setAttribute('y2', m.y2 * 100);
                                line.setAttribute('class', 'plan-viewer-line');
                                const title = document.createElementNS(ns, 'title');
                                title.textContent = tooltip;
                                line.appendChild(title);
                                svg.appendChild(line);

                                const midX = (Number(m.x) + Number(m.x2)) / 2;
                                const midY = (Number(m.y) + Number(m.y2)) / 2;
                                const marker = document.createElement('div');
                                marker.className = 'plan-viewer-pin is-line-marker';
                                marker.style.left = (midX * 100) + '%';
                                marker.style.top = (midY * 100) + '%';
                                marker.textContent = label;
                                marker.title = tooltip;
                                if (interactive) {
                                    marker.addEventListener('click', function (e) {
                                        e.stopPropagation();
                                        openViewer(m, midX, midY);
                                    });
                                }
                                pinsLayer.appendChild(marker);
                            }
                        });

                        renderLegend();
                    }

                    function renderPage(num) {
                        pdfDoc.getPage(num).then(function (page) {
                            const containerWidth = stage.clientWidth || 800;
                            const unscaled = page.getViewport({ scale: 1 });
                            const scale = containerWidth / unscaled.width;
                            const viewport = page.getViewport({ scale });

                            canvas.width = viewport.width;
                            canvas.height = viewport.height;

                            page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function () {
                                loading.classList.add('d-none');
                                drawMarkups();
                            });
                        });
                        pageInfo.textContent = 'Page ' + num + ' of ' + totalPages;
                    }

                    // --- Viewing / deleting an existing markup -------------------------

                    function openViewer(markup, x, y) {
                        if (! viewer) return;
                        if (composer) composer.classList.add('d-none');
                        viewingMarkupId = markup.id;
                        viewer.style.left = (x * 100) + '%';
                        viewer.style.top = (y * 100) + '%';
                        viewerMeta.textContent = (markup.user_name || 'Reviewer') + ' · ' + (markup.type === 'pin' ? 'Pin' : 'Line');
                        viewerComment.textContent = markup.comment || 'No comment.';
                        viewer.classList.remove('d-none');
                    }

                    function closeViewer() {
                        if (! viewer) return;
                        viewer.classList.add('d-none');
                        viewingMarkupId = null;
                    }

                    function deleteMarkup() {
                        if (! viewingMarkupId) return;

                        const btn = viewer.querySelector('.plan-viewer-viewer-delete');
                        const spinner = viewer.querySelector('.plan-viewer-viewer-spinner');
                        const label = viewer.querySelector('.plan-viewer-viewer-delete-label');
                        btn.disabled = true;
                        spinner.classList.remove('d-none');
                        label.textContent = 'Deleting…';

                        const idToDelete = viewingMarkupId;

                        fetch(storeUrl + '/' + idToDelete, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }).then(function (res) {
                            if (! res.ok) throw new Error('delete failed');
                            const idx = markups.findIndex(m => m.id === idToDelete);
                            if (idx !== -1) markups.splice(idx, 1);
                            closeViewer();
                            drawMarkups();
                        }).catch(function () {
                            alert('Could not remove this mark. You can only delete marks you created.');
                        }).finally(function () {
                            btn.disabled = false;
                            spinner.classList.add('d-none');
                            label.textContent = 'Delete';
                        });
                    }

                    if (viewer) {
                        viewer.querySelector('.plan-viewer-viewer-close').addEventListener('click', closeViewer);
                        viewer.querySelector('.plan-viewer-viewer-delete').addEventListener('click', deleteMarkup);
                    }

                    // --- Inline comment composer (creating a new markup) ---------------

                    function openComposer(x, y) {
                        composer.style.left = (x * 100) + '%';
                        composer.style.top = (y * 100) + '%';
                        composer.classList.remove('d-none');
                        composerText.classList.remove('is-invalid');
                        composerText.value = '';
                        composerText.focus();
                    }

                    function clearRubberLine() {
                        if (rubberLine) { rubberLine.remove(); rubberLine = null; }
                    }

                    function clearLineStart() {
                        if (lineStartDot) { lineStartDot.remove(); lineStartDot = null; }
                        lineStart = null;
                        clearRubberLine();
                    }

                    function closeComposer() {
                        composer.classList.add('d-none');
                        if (pending && pending.els) {
                            pending.els.forEach(function (el) { el.remove(); });
                        }
                        pending = null;
                        clearLineStart();
                    }

                    function showPendingMarker(x, y, dotOnly) {
                        const el = document.createElement('div');
                        el.className = 'plan-viewer-pin is-pending' + (dotOnly ? ' is-dot' : '');
                        el.style.left = (x * 100) + '%';
                        el.style.top = (y * 100) + '%';
                        if (! dotOnly) el.textContent = '…';
                        pinsLayer.appendChild(el);
                        return el;
                    }

                    function saveComposer() {
                        if (! pending) return;

                        const saveBtn = composer.querySelector('.plan-viewer-composer-save');
                        const spinner = composer.querySelector('.plan-viewer-composer-spinner');
                        const label = composer.querySelector('.plan-viewer-composer-save-label');
                        saveBtn.disabled = true;
                        spinner.classList.remove('d-none');
                        label.textContent = 'Saving…';

                        const payload = Object.assign({}, pending.data, { comment: composerText.value });

                        fetch(storeUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify(payload),
                        }).then(function (res) {
                            if (! res.ok) throw new Error('save failed');
                            return res.json();
                        }).then(function (markup) {
                            if (pending && pending.els) pending.els.forEach(function (el) { el.remove(); });
                            markups.push(markup);
                            pending = null;
                            composer.classList.add('d-none');
                            drawMarkups();
                        }).catch(function () {
                            composerText.classList.add('is-invalid');
                        }).finally(function () {
                            saveBtn.disabled = false;
                            spinner.classList.add('d-none');
                            label.textContent = 'Save';
                        });
                    }

                    if (composer) {
                        composer.querySelector('.plan-viewer-composer-save').addEventListener('click', saveComposer);
                        composer.querySelector('.plan-viewer-composer-cancel').addEventListener('click', closeComposer);
                        composerText.addEventListener('keydown', function (e) {
                            if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) saveComposer();
                            if (e.key === 'Escape') closeComposer();
                        });
                    }

                    // --- Placing pins / lines --------------------------------------------

                    function onStageMouseMove(e) {
                        if (! interactive || tool !== 'line' || ! lineStart) return;

                        const rect = stage.getBoundingClientRect();
                        const x = (e.clientX - rect.left) / rect.width;
                        const y = (e.clientY - rect.top) / rect.height;

                        if (! rubberLine) {
                            const ns = 'http://www.w3.org/2000/svg';
                            rubberLine = document.createElementNS(ns, 'line');
                            rubberLine.setAttribute('class', 'plan-viewer-line is-rubber');
                            svg.appendChild(rubberLine);
                        }
                        rubberLine.setAttribute('x1', lineStart.x * 100);
                        rubberLine.setAttribute('y1', lineStart.y * 100);
                        rubberLine.setAttribute('x2', x * 100);
                        rubberLine.setAttribute('y2', y * 100);
                    }

                    function onStageClick(e) {
                        closeViewer();
                        if (! interactive || tool === 'none') return;
                        if (composer && !composer.classList.contains('d-none')) return; // finish current one first

                        const rect = stage.getBoundingClientRect();
                        const x = (e.clientX - rect.left) / rect.width;
                        const y = (e.clientY - rect.top) / rect.height;

                        if (tool === 'pin') {
                            const pendingEl = showPendingMarker(x, y);
                            pending = { data: { type: 'pin', page: currentPage, x: x.toFixed(5), y: y.toFixed(5) }, els: [pendingEl] };
                            openComposer(x, y);
                        } else if (tool === 'line') {
                            if (! lineStart) {
                                lineStart = { x: x, y: y };
                                lineStartDot = showPendingMarker(x, y, true);
                                hint.textContent = 'Move to the end point and click to finish the line.';
                            } else {
                                clearRubberLine();
                                if (lineStartDot) { lineStartDot.remove(); lineStartDot = null; }

                                // Draw the actual line immediately — it shows right away,
                                // while the comment is still being written.
                                const ns = 'http://www.w3.org/2000/svg';
                                const previewLine = document.createElementNS(ns, 'line');
                                previewLine.setAttribute('x1', lineStart.x * 100);
                                previewLine.setAttribute('y1', lineStart.y * 100);
                                previewLine.setAttribute('x2', x * 100);
                                previewLine.setAttribute('y2', y * 100);
                                previewLine.setAttribute('class', 'plan-viewer-line');
                                svg.appendChild(previewLine);

                                const midX = (lineStart.x + x) / 2;
                                const midY = (lineStart.y + y) / 2;
                                const pendingEl = showPendingMarker(midX, midY);

                                pending = {
                                    data: {
                                        type: 'line', page: currentPage,
                                        x: lineStart.x.toFixed(5), y: lineStart.y.toFixed(5),
                                        x2: x.toFixed(5), y2: y.toFixed(5),
                                    },
                                    els: [pendingEl, previewLine],
                                };
                                openComposer(midX, midY);
                                lineStart = null;
                                hint.textContent = 'Click the start point of the next line.';
                            }
                        }
                    }

                    if (interactive) {
                        root.querySelectorAll('[data-tool]').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                tool = btn.dataset.tool;
                                closeComposer();
                                closeViewer();
                                root.querySelectorAll('[data-tool]').forEach(b => b.classList.remove('active'));
                                stage.classList.toggle('tool-active', tool !== 'none');
                                if (tool !== 'none') {
                                    btn.classList.add('active');
                                    hint.textContent = tool === 'pin'
                                        ? 'Click anywhere on the drawing to drop a pin.'
                                        : 'Click the start point of the line.';
                                } else {
                                    hint.textContent = 'Select a tool, then click on the drawing. Click an existing mark to edit or delete it.';
                                }
                            });
                        });
                        stage.addEventListener('click', onStageClick);
                        stage.addEventListener('mousemove', onStageMouseMove);
                    }

                    root.querySelector('.plan-viewer-prev').addEventListener('click', function () {
                        if (currentPage > 1) { currentPage--; renderPage(currentPage); }
                    });
                    root.querySelector('.plan-viewer-next').addEventListener('click', function () {
                        if (currentPage < totalPages) { currentPage++; renderPage(currentPage); }
                    });

                    if (! pdfUrl) return;

                    pdfjsLib.getDocument(pdfUrl).promise.then(function (doc) {
                        pdfDoc = doc;
                        totalPages = doc.numPages;
                        renderPage(currentPage);
                    }).catch(function () {
                        loading.classList.add('d-none');
                        stage.insertAdjacentHTML('beforeend', '<p class="text-muted p-4 mb-0">Could not load the drawing preview.</p>');
                    });
                }

                document.querySelectorAll('.plan-viewer').forEach(initViewer);
            })();
            </script>
        @endonce
    @endif
</div>
