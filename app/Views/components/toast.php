<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="margin-top: 20px; z-index: 9999;">
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
        <div id="globalToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #2B3385 !important; color: #ffffff !important;">
            <div class="d-flex">
                <div class="toast-body fw-medium text-start">
                    <?php if (session()->getFlashdata('success')): ?>
                        <i class="fas fa-check-circle me-2" style="color: #4CAF50;"></i> <?= session()->getFlashdata('success') ?>
                    <?php elseif (session()->getFlashdata('error')): ?>
                        <?= session()->getFlashdata('error') ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= base_url('js/toast.js') ?>"></script>
