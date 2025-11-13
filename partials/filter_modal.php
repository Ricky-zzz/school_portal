        <template x-if="showFilter">
            <div>
                <div class="modal fade show d-block" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">
                            <div class="modal-header">
                                <h5 class="modal-title">Search Student by Name</h5>
                                <button type="button" class="btn-close" @click="showFilter=false"></button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group mb-3">
                                    <input class="form-control" placeholder="Enter student name..." x-model="searchName"
                                        @keyup.enter="filterStudent">
                                    <button class="btn btn-primary" @click="filterStudent">Search</button>
                                </div>

                                <template x-if="searchResults.length === 0">
                                    <p class="text-muted">No results yet.</p>
                                </template>

                                <ul class="list-group" x-show="searchResults.length > 0">
                                    <template x-for="stud in searchResults" :key="stud.id">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong x-text="stud.name"></strong>
                                                <small class="text-muted" x-text="'(' + stud.stud_no + ')'"></small>
                                            </span>
                                            <button class="btn btn-success btn-sm"
                                                @click="selectStudent(stud.stud_no)">✔</button>
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