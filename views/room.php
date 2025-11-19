<?php
require("../partials/session.php");
?>

<!DOCTYPE html>
<html lang="en" x-data="roomApp()">

<head>
    <meta charset="UTF-8">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body>
    <?php
    $activePage = 'room';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3"> Room</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>
        <?php
        include "../partials/alert.php";
        ?>

        <form class="row g-3" @submit.prevent>
            <div class="col-md-6">
                <input type="text" class="form-control" x-model="roomName" autofocus required placeholder="Room Name">
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary bg-info" @click="addRoom(roomName)">ADD</button>
            </div>
        </form>

        <div class="mt-4" x-show="rooms && rooms.length > 0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr class="table-dark">
                            <th>Room</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="room in rooms" :key="room.id">
                            <tr>
                                <td x-text="room.room_name"></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        @click="roomEdit(room.id, room.room_name)">Edit</button>
                                    <button class="btn btn-danger btn-sm"
                                        @click="deleteRoom(room.id)">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================== EDIT MODAL ===================== -->
        <template x-if="showEdit">
            <div>
                <div class="modal fade show d-block" tabindex="-1" x-cloak @keydown.escape.window="showEdit=false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Room</h5>
                                <button type="button" class="btn-close" @click="showEdit=false"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Room Name</label>
                                    <input type="text" class="form-control" x-model="e_room" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    @click="showEdit=false">Close</button>
                                <button type="button" class="btn btn-primary"
                                    @click="updateRoom(e_id, e_room)">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-backdrop fade show"></div>
            </div>
        </template>

        <script>
            function roomApp() {
                return {
                    showEdit: false,
                    rooms: [],
                    alert: null,
                    roomName: '',
                    e_id: null,
                    e_room: '',

                    init() {
                        this.fetchRooms();
                    },

                    async fetchRooms() {
                        try {
                            let res = await fetch("../sql/room_c.php?action=get_rooms");
                            let data = await res.json();;
                            if (data.success) {
                                this.rooms = data.rooms;
                            }
                        } catch (error) {
                            console.error("Error fetching rooms:", error);
                            this.alert = "Error loading rooms";
                        }
                    },

                    async addRoom(roomName) {
                        if (!roomName || roomName.trim() === '') {
                            this.alert = 'Please enter a room name';
                            return;
                        }

                        try {
                            let formData = new FormData();
                            formData.append("action", "add");
                            formData.append("newRoom", roomName.trim());

                            let res = await fetch("../sql/room_c.php", {
                                method: "POST",
                                body: formData
                            });
                            let data = await res.json();

                            this.alert = data.message;
                            if (data.success) {
                                this.rooms = data.rooms;
                                this.roomName = '';
                            }
                        } catch (error) {
                            console.error("Error adding room:", error);
                            this.alert = "Error adding room";
                        }
                    },

                    async deleteRoom(id) {
                        if (!confirm("Are you sure you want to delete this room?")) {
                            return;
                        }

                        try {
                            let formData = new FormData();
                            formData.append("action", "delete");
                            formData.append("del_id", id);

                            let res = await fetch("../sql/room_c.php", {
                                method: "POST",
                                body: formData
                            });
                            let data = await res.json();

                            this.alert = data.message;
                            if (data.success) {
                                this.rooms = data.rooms;
                            }
                        } catch (error) {
                            console.error("Error deleting room:", error);
                            this.alert = "Error deleting room";
                        }
                    },

                    roomEdit(room_id, room_name) {
                        this.e_id = room_id;
                        this.e_room = room_name;
                        this.showEdit = true;
                    },

                    async updateRoom(id, newName) {
                        if (!newName || newName.trim() === '') {
                            this.alert = 'Please enter a room name';
                            return;
                        }

                        try {
                            let formData = new FormData();
                            formData.append("action", "edit");
                            formData.append("edit_id", id);
                            formData.append("newName", newName.trim());

                            let res = await fetch("../sql/room_c.php", {
                                method: "POST",
                                body: formData
                            });
                            let data = await res.json();

                            this.alert = data.message;
                            if (data.success) {
                                this.rooms = data.rooms;
                                this.showEdit = false;
                            }
                        } catch (error) {
                            console.error("Error updating room:", error);
                            this.alert = "Error updating room";
                        }
                    }
                }
            }
        </script>


</body>

</html>