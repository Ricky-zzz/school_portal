<template x-if="showFilter">
    <div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Search Receipts by Student Name</h5>
                        <button type="button" class="btn-close" @click="showFilter = false"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Search Bar -->
                        <div class="input-group mb-3">
                            <input class="form-control" placeholder="Enter student name..." 
                                   x-model="searchName"
                                   @keyup.enter="filterStudent">
                            <button class="btn btn-info" @click="filterStudent">Search</button>
                        </div>

                        <!-- Empty Results -->
                        <template x-if="searchResults.length === 0">
                            <p class="text-muted">No results found.</p>
                        </template>

                        <!-- Results List -->
                        <ul class="list-group" x-show="searchResults.length > 0">
                            <template x-for="stud in searchResults" :key="stud.student_id">
                                <li class="list-group-item">
                                    <!-- Student Header -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong x-text="stud.student_name"></strong>
                                            <small class="text-muted" x-text="'(' + stud.stud_no + ')'"></small>
                                            <div class="text-muted small" x-text="stud.course_name"></div>
                                        </div>
                                    </div>

                                    <!-- OR Numbers -->
                                    <div class="mt-2">
                                        <div class="fw-semibold mb-1">Recent OR Numbers:</div>
                                        <template x-if="stud.or_numbers && stud.or_numbers.length > 0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <template x-for="or in stud.or_numbers" :key="or.or_number">
                                                    <button class="btn btn-outline-success btn-sm"
                                                            @click="selectReceipt(or.or_number)">
                                                        <span x-text="or.or_number"></span>
                                                        <small class="text-muted" x-text="'(' + or.or_date + ')'"></small>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!stud.or_numbers || stud.or_numbers.length === 0">
                                            <p class="text-muted small mb-0">No receipts found.</p>
                                        </template>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</template>
