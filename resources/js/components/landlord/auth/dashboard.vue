<template>
    <div class="main-content w-100">




        <div class="dashboard-content px-2 px-md-4 py-3">
            <NotificationList ref="toastRef" />
            <Loader ref="loader" />




            <!-- Header Card -->
            <div class="py-3 px-3 px-md-4 mb-3 bg-light border-start border-primary border-4 rounded shadow-sm">
                <h3 class="mb-2 text-primary">
                    <i class="bi bi-person-circle me-2"></i>
                    {{ landlord.firstname }} {{ landlord.lastname }}
                </h3>




                <!-- Date & Reports -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-3 gap-2">
                    <!-- Date -->
                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                        <label class="form-label fw-bold m-0">📅 Today's Date:</label>
                        <input type="date" class="form-control w-25 w-md-auto"
                            style="border: 1px solid #4edce2; min-width: 50px;" v-model="newDate" :max="today">
                    </div>


                    <div class="ms-md-auto mt-2 mt-md-0">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button"
                                id="reportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ selectedDorm ? selectedDorm.dormName : 'Choose Dorm' }}
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="reportDropdown">
                                <li v-for="dorm in dorms" :key="dorm.dormID">
                                    <a class="dropdown-item" href="#" @click.prevent="selectDorm(dorm)">
                                        {{ dorm.dormName }}
                                    </a>
                                </li>
                            </ul>
                        </div>


                    </div>
                    <!-- Download Report -->
                    <div class="ms-md-auto mt-2 mt-md-0">
                        <a :href="`/generate-full-report/${landlord_id}?date=${newDate}`" target="_blank"
                            class="btn btn-outline-success w-100" :class="{ 'disabled': !newDate }">
                            📄 Download Full Report
                        </a>
                    </div>
                </div>
            </div>




            <!-- Info Cards -->
            <div class="row">
                <!-- Total Tenants -->
                <div class="col-12 col-md-6 mb-3">
                    <a :href="`/all-tenants-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-primary border-4 h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-primary">Total Tenants</h5>
                                    <p class="card-text display-6 fw-bold mb-0 text-primary">{{ totalTenants }}</p>
                                </div>
                                <i class="bi bi-people-fill fs-1 text-primary"></i>
                            </div>
                        </div>
                    </a>
                </div>




                <!-- Vacant Beds -->
                <div class="col-12 col-md-6 mb-3">
                    <a :href="`/landlordRoomManagement/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-success">Vacant Beds</h5>
                                    <p class="card-text display-6 fw-bold mb-0 text-success">{{ availableBeds }}</p>
                                </div>
                                <i class="bi bi-door-open-fill fs-1 text-success"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>




            <!-- Charts -->
            <div class="charts d-flex flex-wrap gap-3 mb-4">
                <!-- Highest Dorm Profits -->
                <div class="chart-container p-3 border rounded shadow-sm flex-grow-1"
                    style="flex: 1 1 45%; min-width: 250px;">
                    <h6 class="fw-bold mb-2">📈 Highest Rooms Profits</h6>
                    <LineChart v-if="chartData" :chart-data="chartData" :chart-options="chartOptions" />
                </div>


                <!-- Profits Per Dorm -->
                <div class="chart-container p-3 border rounded shadow-sm flex-grow-1"
                    style="flex: 1 1 45%; min-width: 250px;">
                    <h6 class="fw-bold mb-2"><i class="bi bi-person-lines-fill me-2"></i> Occupants by Gender</h6>
                    <DoughnutChart v-if="bookingChartData" :chart-data="bookingChartData"
                        :chart-options="bookingChartOptions" />


                    <!-- Legend -->
                    <div class="legend mt-3" v-if="bookingChartData?.labels?.length">
                        <div class="legend-item d-flex justify-content-between align-items-center mb-1"
                            v-for="(label, index) in bookingChartData.labels" :key="index">
                            <span class="dot me-2" :style="{
                                width: '10px', height: '10px', backgroundColor: bookingChartData.datasets[0].backgroundColor[index], borderRadius: '50%'
                            }"></span>
                            <span class="flex-grow-1 small">{{ label }}</span>
                            <span class="small">
                                {{ calculatePercentage(bookingChartData.datasets[0].data[index],
                                    bookingChartData.datasets[0].data) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>






            <!-- Recent Bookings & Reservations -->
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <a :href="`/booking-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-info border-4 h-100">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 text-info"><i class="bi bi-calendar-check-fill me-2"></i>Recent Bookings
                                </h5>
                                <span class="badge bg-info text-white">Updated</span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Move-In</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="booking in bookings.slice(0, 3)" :key="booking.bookingID">
                                            <td><strong>{{ booking.firstname }} {{ booking.lastname }}</strong></td>
                                            <td><span class="text-muted">{{ booking.moveInDate }}</span></td>
                                            <td><span class="badge bg-primary px-3 py-2">Room {{
                                                booking.room?.roomNumber ?? 'N/A' }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </a>
                </div>




                <div class="col-12 col-md-6 mb-3">
                    <a :href="`/reservation-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-header bg-transparent d-flex align-items-center">
                                <h5 class="mb-0 text-warning"><i class="bi bi-person-plus-fill me-2"></i>Recent
                                    Reservations</h5>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="tenant in reservations.slice(0, 3)" :key="tenant.reservationID">
                                            <td><strong>{{ tenant.firstname }} {{ tenant.lastname }}</strong></td>
                                            <td><span class="badge bg-primary px-3 py-2">Room {{ tenant.room?.roomNumber
                                                ?? 'N/A' }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </a>
                </div>
            </div>




        </div>
    </div>
</template>
<script>
import axios from 'axios';
import LineChart from './chart/LineChart.vue';
import DoughnutChart from './chart/DoughnuChart.vue';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';
import { Title } from 'chart.js';
import { get } from 'lodash';






export default
    {
        components: {
            LineChart,
            DoughnutChart,
            Loader,
            NotificationList


        },
        data() {
            return {
                receiverID: '',
                hasSubscribed: false,
                landlord_id: '',
                landlord: [],
                reservations: [],
                notifications: [],
                rooms: [],
                dorms: [],
                selectedDorm: null,
                bookings: [],
                newDate: '',
                today: '',
                totalTenants: 0,
                availableBeds: 0,
                totalRoomProfit: 0,
                chartData: null,
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
                bookingChartData: null,
                bookingChartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            }
        },


        mounted() {
            const element = document.getElementById('dashboard');
            this.landlord_id = element.dataset.landlordId;
            this.receiverID = this.landlord_id;  // set receiverID here, early
            this.subscribeToNotifications();
            this.today = this.getTodayDate();
            this.newDate = this.today; // default value to today
            this.getLandlord();


        },
        methods:
        {
            subscribeToNotifications() {
                if (this.hasSubscribed) return; // prevent multiple subscriptions
                this.hasSubscribed = true;


                this.receiverID = this.landlord_id;
                Echo.private(`notifications.${this.receiverID}`)
                    .subscribed(() => {
                        console.log('✔ Subscribed!');
                    })
                    .listen('.NewNotificationEvent', (e) => {
                        this.notifications.unshift(e); // save for list
                        this.$refs.toastRef.pushNotification({
                            title: e.title || 'New Notification',
                            message: e.message,
                            color: 'success',
                        });
                    });
            },
            async getLandlord() {
                try {
                    this.$refs.loader.loading = true;


                    const response = await axios.get(`/get/landlord/${this.landlord_id}`);


                    this.landlord = response.data.landlord;
                    await Promise.all([
                        this.getTotalTenants(),
                        this.getAvailableBeds(),
                        this.getReservationList(),
                        this.getBookingList(),
                        this.getRoomProfits(),
                        this.getGenderDistribution(),
                        this.getDormID()
                    ]);


                }
                catch (error) {
                    console.log(error);
                }
                finally {
                    this.$refs.loader.loading = false;


                }


            },
            selectDorm(dorm) {
                this.selectedDorm = dorm;
                // Call your function to fetch room profits for this dorm
                this.getRoomProfits(dorm.dormID);
                this.getGenderDistribution(dorm.dormID);
                this.getTotalTenants(dorm.dormID);
                this.getAvailableBeds(dorm.dormID);
                this.getReservationList(dorm.dormID);
                this.getBookingList(dorm.dormID);
            },
            async getTotalTenants(dorm_id = null) {
                try {
                    // Build params object
                    const params = { date: this.newDate };
                    if (dorm_id) {
                        params.dorm_id = dorm_id;
                    }


                    const response = await axios.get(`/get/total-tenants/${this.landlord_id}`, { params });
                    this.totalTenants = response.data.total_tenants;
                } catch (error) {
                    console.error('Failed to fetch total tenants:', error);
                    this.totalTenants = 0;
                }
            },


            async getAvailableBeds(dorm_id = null) {
                try {
                    // Build params object
                    const params = { date: this.newDate };
                    if (dorm_id) {
                        params.dorm_id = dorm_id;
                    }


                    const response = await axios.get(`/get/available-beds/${this.landlord_id}`, { params });
                    this.availableBeds = response.data.available_beds;
                } catch (error) {
                    console.error('Failed to fetch available beds:', error);
                }
            },
            async getReservationList(dorm_id = null) {
                try {
                    const response = await axios.get(`/get/reservation-list/${this.landlord_id}`, {
                        params: { date: this.newDate, dorm_id }
                    });
                    this.reservations = response.data.reservations;
                } catch (error) {
                    console.error('Failed to fetch tenant list:', error);
                }
            },
            async getBookingList(dorm_id = null) {
                try {
                    const response = await axios.get(`/get/booking-list/${this.landlord_id}`, {
                        params: { date: this.newDate, dorm_id }
                    });
                    this.bookings = response.data.bookings;
                } catch (error) {
                    console.error('Failed to fetch booking list:', error);
                }
            },
            async getDormID() {
                try {
                    const response = await axios.get(`/get/dorm-id/${this.landlord_id}`);
                    this.dorms = response.data.dorms;
                } catch (error) {
                    console.error('Failed to fetch dorm IDs:', error);
                }
            },


            async getRoomProfits(dorm_id = null) {
                try {
                    const params = { date: this.newDate };
                    if (dorm_id) params.dorm_id = dorm_id;


                    const response = await axios.get(`/get/room-profits/${this.landlord_id}`, { params });
                    const rooms = response.data.data;


                    this.chartData = {
                        labels: rooms.map(r => r.roomNumber), // ← roomNumber, dili roomName
                        datasets: [
                            {
                                label: 'Room Profits',
                                data: rooms.map(r => r.profit), // ← profit field
                                borderColor: '#2196f3',
                                tension: 0.4,
                                fill: false
                            }
                        ]
                    };


                    this.totalRoomProfit = response.data.total_profit;
                } catch (error) {
                    console.error('Error fetching room profits:', error);
                }
            },
            async getGenderDistribution(dorm_id = null) {
                try {
                    this.$refs.loader.loading = true;


                    const response = await axios.get(`/get/gender-distribution/${this.landlord_id}`, {
                        params: {
                            date: this.newDate,
                            dorm_id: dorm_id
                        }
                    });


                    const genders = Array.isArray(response.data?.data) ? response.data.data : [];


                    if (genders.length === 0) {
                        this.bookingChartData = {
                            labels: ["No Data"],
                            datasets: [
                                {
                                    label: "Gender Distribution",
                                    data: [1],
                                    backgroundColor: ["#e0e0e0"],
                                    hoverOffset: 4
                                }
                            ]
                        };
                        return;
                    }


                    const labels = genders.map(item => item.gender || "Unknown");
                    const data = genders.map(item => item.count || 0);


                    const backgroundColors = ["#2196f3", "#e91e63", "#ff9800", "#4caf50", "#9c27b0"];


                    this.bookingChartData = {
                        labels,
                        datasets: [
                            {
                                label: "Gender Distribution",
                                data,
                                backgroundColor: backgroundColors.slice(0, data.length),
                                hoverOffset: 4
                            }
                        ]
                    };


                } catch (error) {
                    console.error("❌ Failed to fetch gender distribution:", error);
                    this.bookingChartData = { labels: [], datasets: [] };
                } finally {
                    this.$refs.loader.loading = false;
                }
            },


            calculatePercentage(value, dataArray) {
                const total = dataArray.reduce((sum, val) => sum + val, 0);
                if (total === 0) return 0;
                return ((value / total) * 100).toFixed(1);
            },
            getTodayDate() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            },
            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleString();
            }




        },
        watch: {
            newDate(newVal) {
                if (newVal) {
                    this.getLandlord();
                }
            },
            landlord_id(newVal) {
                if (newVal) {
                    this.subscribeToNotifications();
                }
            }
        }
    }


</script>