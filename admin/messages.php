<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/MessageController.php';

$messageController = new MessageController();
$messages = $messageController->index();
$unreadCount = $messageController->getUnreadCount();

// Opened from the top bar's Messages dropdown
$openId = isset($_GET['open']) ? (int) $_GET['open'] : 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../src/assets/css/admin/admin-dashboard.css" rel="stylesheet">
    <link href="../src/assets/css/admin/left-side-bar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/top-navbar.css" rel="stylesheet">
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-collapsed');</script>
    <style>
        .message-row { cursor: pointer; }
        .message-row.unread td { font-weight: 600; }
        .message-row.unread .unread-dot { display: inline-block; }
        .unread-dot { display: none; width: 8px; height: 8px; border-radius: 50%; background: #D35400; margin-right: 6px; }
        .message-preview { max-width: 360px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #6c757d; font-weight: 400 !important; }
        #mdBody { white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="dashboard-app">
        <?php $adminPage = 'messages'; include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="dashboard-wrapper">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

            <main class="dashboard-main">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h1 class="h5 mb-1 fw-bold">Messages
                        <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                Messages customers send from the Contact Us page. Open one to read it and reply by email.</span>
                        </span>
                    </h1>
                    <div class="small text-muted"><?= count($messages) ?> total &middot; <?= $unreadCount ?> unread</div>
                </div>
            </div>

            <div class="dashboard-card p-4 mt-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="messageSearch" class="form-control border-start-0 ps-0" placeholder="Search name, email or subject...">
                    </div>
                    <div class="d-flex gap-2">
                        <select id="messageFilter" class="form-select form-select-sm" style="width: auto;" aria-label="Filter messages">
                            <option value="all">All messages</option>
                            <option value="unread">Unread only</option>
                        </select>
                        <?php if ($unreadCount > 0): ?>
                            <button class="btn btn-light btn-sm px-3" id="markAllReadBtn">
                                <i class="bi bi-check2-all me-1"></i> Mark all read
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Received</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="messageTableBody">
                            <?php if (empty($messages)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No messages yet. They will appear here when customers use the Contact Us page.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="message-row<?= $msg['is_read'] ? '' : ' unread' ?>" data-message-id="<?= (int) $msg['message_id'] ?>"
                                    data-search="<?= htmlspecialchars(strtolower($msg['name'] . ' ' . $msg['email'] . ' ' . $msg['subject'])) ?>">
                                    <td>
                                        <span class="unread-dot" aria-hidden="true"></span><?= htmlspecialchars($msg['name']) ?>
                                        <div class="small text-muted fw-normal"><?= htmlspecialchars($msg['email']) ?><?= $msg['user_id'] ? ' &middot; customer account' : '' ?></div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($msg['subject']) ?>
                                        <div class="small message-preview"><?= htmlspecialchars($msg['message']) ?></div>
                                    </td>
                                    <td class="text-nowrap"><?= date('M d, Y g:i A', strtotime($msg['created_at'])) ?></td>
                                    <td class="text-end text-nowrap">
                                        <button class="btn btn-light btn-sm" title="Open" data-open-message="<?= (int) $msg['message_id'] ?>">
                                            <i class="bi bi-envelope-open"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger" title="Delete" data-delete-message="<?= (int) $msg['message_id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="noMatchRow" style="display: none;"><td colspan="4" class="text-center py-4 text-muted">No messages match.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </main>
        </div>
    </div>

    <!-- MESSAGE DETAILS MODAL -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="mdSubject" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="mdSubject">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 small text-muted mb-3">
                        <div><span class="fw-semibold text-dark" id="mdName"></span> &lt;<span id="mdEmail"></span>&gt;</div>
                        <div id="mdDate"></div>
                    </div>
                    <div class="p-3 bg-light rounded" id="mdBody"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light text-danger me-auto" id="mdDeleteBtn"><i class="bi bi-trash me-1"></i>Delete</button>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                    <a class="btn btn-primary px-4" id="mdReplyBtn" href="#"><i class="bi bi-reply me-1"></i>Reply by Email</a>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash text-danger fs-1"></i>
                    <h6 class="fw-bold mt-2">Delete this message?</h6>
                    <p class="small text-muted mb-0">This can't be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteMessageBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script>const OPEN_MESSAGE_ID = <?= $openId ?>;</script>
    <script src="../src/assets/javascript/admin/messages.js"></script>
</body>
</html>
