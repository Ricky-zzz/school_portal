<!-- Alert -->
<template x-if="alert">
    <div class="modal fade show d-block" tabindex="-1" >
        <div class="modal-dialog modal-dialog-centered " >
            <div class="modal-content p-4 bg-warning fw-bold font-monospace border-warning-subtle">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold  text-align-center fs-4">Attention!</h5>
                </div>
                <div class="modal-body">
                    <p x-text="alert" class="fs-4 fw-bold  text-primary-emphasis font-monospace"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" @click="alert = null">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
</template>