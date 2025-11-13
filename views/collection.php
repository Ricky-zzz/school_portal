<?php
require("../partials/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <?php
    $activePage = 'collection';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4" x-data="zaCollection()" x-init="initForm()">
        <div class="my-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <h2 class="mb-0 me-3">Collections</h2>
                <a href="../menu.php" class="btn btn-danger d-flex align-items-center me-2">
                    <i class="bi bi-box-arrow-left me-1"></i> Back
                </a>
                <button class="btn btn-warning d-flex align-items-center" @click="window.location.reload()">
                    <i class="bi bi-plus-circle me-1"></i> New Receipt
                </button>
            </div>

            <div class="fw-bold">
                Logged in as:
                <span class="text-primary">
                    <?= htmlspecialchars($_SESSION["username"] ?? "Unknown") ?>
                </span>
            </div>
        </div>


        <?php include "../partials/alert.php"; ?>

        <!-- Card for Receipt and Student Info -->
        <div class="card p-4 mb-4">
            <div class="mb-4 d-flex justify-content-between w-100 g-2">
                <!-- Official Receipt -->
                <div class="w-50">
                    <label>Official Receipt #</label>
                    <input type="text" class="form-control" x-model="or_no" placeholder="Enter OR #"
                        @input.debounce.2000ms="searchReceipt()">

                </div>
                <button class="btn btn-info mx-1" @click="showFilter = true">
                    <i class="bi bi-filter-square me-1"></i>Filter
                </button>


                <!-- Date -->
                <div class="w-50 mx-3">
                    <label>Date</label>
                    <input type="date" class="form-control" x-model="receiptDate" readonly>
                </div>
            </div>

            <!-- Student Search -->
            <div class="mb-4 w-50">
                <label>Student #</label>
                <div class="input-group">
                    <input type="text" class="form-control" x-model="stud_no" autofocus placeholder="Enter Student #"
                        @keyup.enter="fetchStudent()">

                </div>
            </div>

            <!-- Student Info -->
            <template x-if="student">
                <div class="mt-4">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label>Student Name</label>
                            <input type="text" class="form-control" :value="student?.name || ''" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label>Course</label>
                            <input type="text" class="form-control" :value="student?.course || ''" readonly>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Semester Selection -->
            <template x-if="student">
                <div class="row my-3 w-50">
                    <div class="col-12">
                        <div x-show="availableSemesters.length > 0">
                            <label class="form-label">Semester:</label>
                            <div class="input-group">
                                <select x-model="semester" class="form-select" @change="fetchBalance"
                                    x-ref="semesterSelect">
                                    <option value="">Select a semester</option>
                                    <template x-for="sem in availableSemesters" :key="sem.id">
                                        <option :value="sem.id" x-text="sem.code"></option>
                                    </template>
                                </select>
                                <button class="btn btn-primary mx-2" @click="viewLedger">
                                    <i class="bi bi-printer mx-2"></i> View/Print Ledger
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Payment Form -->
        <template x-if="student">
            <div class="card p-4 mt-4">
                <div class="row">
                    <!-- Payment Method -->
                    <div class="col-12 col-md-6 mb-3">
                        <label for="paymentMethod">Payment Method</label>
                        <select id="paymentMethod" class="form-select" x-model="paymentMethod">
                            <option value="">Select a method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>
                </div>

                <!-- Cash Fields -->
                <div class="row" x-show="paymentMethod === 'cash'">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="balanceCash">Cash</label>
                        <input type="number" id="balanceCash" class="form-control" x-model="balance">
                    </div>
                </div>

                <!-- GCash Fields -->
                <div class="row" x-show="paymentMethod === 'gcash'">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="balanceGCash">GCash</label>
                        <input type="number" id="balanceGCash" class="form-control" x-model="balance">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="reference">GCash Reference #</label>
                        <input type="text" id="reference" class="form-control" x-model="reference"
                            placeholder="Enter Reference #">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-danger me-2" x-show="!editing" @click="confirmTransaction">
                        Commit Transaction
                    </button>
                    <button class="btn btn-warning me-2" x-show="editing" @click="confirmTransaction">
                        Edit Transaction
                    </button>
                    <button class="btn btn-danger" x-show="editing"
                        @click="if (confirm('Are you sure you want to DELETE this transaction?')) deleteTransaction()">
                        Delete
                    </button>

                </div>
            </div>
        </template>

        <!-- Filter Modal -->
        <?php
        include "../partials/filter_r.php";
        ?>


    </div>

    <script>
        function zaCollection() {
            return {
                stud_no: "",
                student: null,
                semester: null,
                availableSemesters: [],
                balance: 0,
                paymentMethod: "",
                reference: "",
                or_no: "",
                receiptDate: "",
                editing: false,
                alert: null,
                showFilter: false,
                searchResults: [],
                searchName: "",

                async initForm() {
                    const today = new Date().toISOString().split("T")[0];
                    this.receiptDate = today;
                    await this.fetchLastOR();
                },

                // Fetch last OR 
                async fetchLastOR() {
                    try {
                        const res = await fetch("../sql/collection_c.php?action=get_last_or");
                        const data = await res.json();
                        this.or_no = data.next_or ?? "000000";
                        this.editing = false;
                    } catch (err) {
                        console.error(err);
                        this.or_no = "000000";
                    }
                },

                // Search OR 
                async searchReceipt() {
                    if (!this.or_no) return;

                    try {
                        const res = await fetch(`../sql/collection_c.php?action=search_receipt&or_number=${this.or_no}`);
                        const data = await res.json();
                        console.log(data);

                        if (data.success && data.receipt) {
                            const r = data.receipt;
                            this.editing = true;
                            this.student = r.student;

                            this.stud_no = r.student.stud_no;

                            const cashAmount = parseFloat(r.cash);
                            const gcashAmount = parseFloat(r.gcash);

                            this.paymentMethod = cashAmount > 0 ? 'cash' : 'gcash';
                            this.balance = cashAmount > 0 ? cashAmount : gcashAmount;
                            this.reference = r.gcash_refno || "";

                            this.reference = r.gcash_refno || "";

                            if (!this.availableSemesters.length) {
                                const semRes = await fetch("../sql/collection_c.php?action=get_semester");
                                this.availableSemesters = await semRes.json();
                            }

                            this.semester = r.semester_id ?? null;
                        } else {
                            this.editing = false;
                        }
                    } catch (err) {
                        console.error(err);
                    }
                },

                // Fetch student info by student number
                async fetchStudent() {
                    if (!this.stud_no) return;
                    const res = await fetch(`../sql/collection_c.php?action=get_student&stud_no=${this.stud_no}`);
                    const data = await res.json();

                    if (data.error) {
                        alert(data.message);
                        return;
                    }

                    this.student = data;

                    if (!this.availableSemesters.length) {
                        const semRes = await fetch("../sql/collection_c.php?action=get_semester");
                        this.availableSemesters = await semRes.json();
                    }

                    this.$nextTick(() => {
                        this.$refs.semesterSelect?.focus();
                    });
                },

                // Fetch balance for selected semester
                async fetchBalance() {
                    if (!this.student || !this.semester) return;
                    const res = await fetch(`../sql/collection_c.php?action=get_balance&stud_id=${this.student.id}&sem_id=${this.semester}`);
                    const data = await res.json();

                    if (data.error) return alert(data.message);

                    this.balance = data.balance;
                    this.paymentMethod = "";
                    this.reference = "";
                },

                // DELETE reciept
                async deleteTransaction() {
                    if (!this.editing || !this.or_no) return;

                    let formData = new FormData();
                    formData.append("action", "deleteTransaction");
                    formData.append("or_number", this.or_no);

                    const res = await fetch("../sql/collection_c.php", {
                        method: "POST",
                        body: formData,
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.resetForm();
                    }
                    this.alert = data.message;

                },

                // Commit or Edit transaction
                async commitTransaction() {
                    if (!this.student || !this.semester || !this.paymentMethod) return;

                    let formData = new FormData();
                    formData.append("action", this.editing ? "edit_transaction" : "commit_transaction");
                    formData.append("student_id", this.student.id);
                    formData.append("semester_id", this.semester);
                    formData.append("or_number", this.or_no);
                    formData.append("payment", this.balance);
                    formData.append("method", this.paymentMethod);
                    formData.append("reference", this.reference);

                    const res = await fetch("../sql/collection_c.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await res.json();
                    this.alert = data.message;
                },

                // Confirm transaction
                confirmTransaction() {
                    if (!this.student || !this.semester || !this.paymentMethod) {
                        alert("Please complete all required fields.");
                        return;
                    }

                    const reference = this.paymentMethod === "gcash" ? this.reference : "N/A";

                    const message =
                        (this.editing ? "UPDATE TRANSACTION?\n\n" : "COMMIT TRANSACTION?\n\n") +
                        `Transaction Details:\n` +
                        `OR #: ${this.or_no}\n` +
                        `Date: ${this.receiptDate}\n` +
                        `Student: ${this.student.name}\n` +
                        `Method: ${this.paymentMethod.toUpperCase()}\n` +
                        `Amount: ₱${this.balance}\n` +
                        (this.paymentMethod === "gcash" ? `Reference: ${reference}` : "");

                    if (confirm(message)) {
                        this.commitTransaction();
                    }
                },

                // View/Print ledger
                viewLedger() {
                    if (!this.student || !this.semester) return;
                    window.open(`../prints/student_ledger.php?stud_id=${this.student.id}&sem_id=${this.semester}`, "_blank");
                },

                // Reset form
                resetForm() {
                    this.stud_no = "";
                    this.student = null;
                    this.semester = null;
                    this.availableSemesters = [];
                    this.balance = 0;
                    this.paymentMethod = "";
                    this.reference = "";
                    this.or_no = "";
                    this.editing = false;
                    this.alert = null;
                    this.fetchLastOR();
                },

                async filterStudent() {
                    if (!this.searchName) return;
                    let res = await fetch(`../sql/collection_c.php?action=search_student&name=${this.searchName}`);
                    this.searchResults = await res.json();
                },

                selectReceipt(or_no) {
                    this.or_no = or_no;
                    this.searchReceipt();
                    this.showFilter = false;
                }
            }
        }
    </script>



</body>

</html>