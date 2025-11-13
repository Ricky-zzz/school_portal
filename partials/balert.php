        <?php if (isset($_GET['msg'])): ?>
            <div class="modal fade show d-block" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content p-4 bg-warning fw-bold font-monospace border-warning-subtle">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-center fs-4">Attention!</h5>
                        </div>
                        <div class="modal-body">
                            <p class="fs-4 fw-bold text-primary-emphasis font-monospace">
                                <?= htmlspecialchars($_GET['msg']) ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-light">Dismiss</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        <?php endif; ?>
