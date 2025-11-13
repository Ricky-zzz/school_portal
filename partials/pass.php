
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="../sql/passChange.php" class="modal-content">
            <input type="hidden" name="action" value="change_password">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="password" class="form-control mb-2" name="old_password" placeholder="Old Password" autofocus required>
                <input type="password" class="form-control mb-2" name="new_password" placeholder="New Password" required>
                <input type="password" class="form-control mb-2" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>