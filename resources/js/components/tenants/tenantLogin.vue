<template>
    <div class="card border-0 shadow-lg mt-5 py-4 px-3 mx-auto w-100"
        style="max-width: 700px; border-radius: 25px; background: #ffffff;">


        <!-- Header -->
        <div class="text-center mb-4 px-2">
            <h1 class="fw-bold text-primary mb-2">🏠 Hello Tenant</h1>
            <p class="text-muted fs-6 mb-0">
                Welcome back! Please log in to explore available dorms and manage your bookings.
            </p>
        </div>


        <!-- Tenant Login Form -->
        <form @submit.prevent="TenantLogin" class="px-3">
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                <input type="email" id="email" v-model="email" class="form-control p-3 shadow-sm rounded border-2"
                    placeholder="Enter your email" style="border-color: #4edce2;">
                <span v-if="errors.email" class="text-danger small mt-1 d-block">
                    {{ errors.email[0] }}
                </span>
            </div>


            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-dark">Password</label>
                <div class="input-group shadow-sm rounded border-2" style="border-color: #4edce2;">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="password"
                        class="form-control border-0 p-3" placeholder="Enter your password">
                </div>
                <span v-if="errors.password" class="text-danger small mt-1 d-block">
                    {{ errors.password[0] }}
                </span>
            </div>


            <!-- Show Password -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="show-password" @click="toggleShowPassword">
                <label class="form-check-label" for="show-password">Show Password</label>
            </div>


            <!-- Sign In Button -->
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary rounded-pill py-2 shadow-sm"
                    style="background: linear-gradient(135deg, #4edce2, #1fb6ff); border: none; font-weight: 600;">
                    Sign In
                </button>
            </div>
        </form>


        <!-- Signup Link -->
        <p class="text-center mt-4 mb-2 text-muted">
            Don’t have an account?
            <a @click="clickSignupLink" class="text-decoration-none text-primary fw-semibold" style="cursor: pointer;">
                Sign up here
            </a>
        </p>


        <!-- Toast Notification -->
        <div :class="['container-toast mt-4', { show: toaster }]" v-show="toaster">
            <div :class="['toast-child', `bg-${toastColor}`]" style="border-radius: 12px; overflow: hidden;">
                <div class="toast-body d-flex justify-content-between align-items-center text-white fw-bold py-3 px-4">
                    <span class="text-wrap">{{ messageToaster }}</span>
                    <button type="button" class="btn-close btn-close-white ms-3" @click="ExitToaster"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
export default {
    data() {
        return {
            email: "",
            password: "",
            messageToaster: "",
            errors: {},
            toaster: false,
            toastColor: ""
        };

    },
    methods: {
        async TenantLogin() {
            if (this.loginValidation()) {

                const formData = new FormData();
                formData.append('email', this.email);
                formData.append('password', this.password);

                try {
                    const response = await axios.post('/tenant-login', formData, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }

                    });
                    if (response.data.status === "success") {
                        const userId = response.data.tenant.id;

                        const userName = response.data.tenant.firstname;
                        localStorage.setItem('tenant', JSON.stringify(response.data.tenant));
                        window.location.href = `/homepage/${userId}`;
                        return true;
                    }
                }

                catch (error) {
                    this.toaster = true;

                    if (error.response) {
                        if (error.response.status === 422) {
                            // Validation errors
                            this.toastColor = "danger";
                            this.messageToaster = "Please check your email and password.";
                        }
                        else if (error.response.status === 401) {
                            // Wrong credentials
                            this.toastColor = "danger";
                            this.messageToaster = error.response.data.message || "Invalid login credentials.";
                        }
                        else if (error.response.status === 403) {
                            // Deactivated account
                            this.toastColor = "warning";
                            this.messageToaster = error.response.data.message || "Your account is deactivated.";
                        }
                        else {
                            // Other errors
                            this.toastColor = "danger";
                            this.messageToaster = error.response.data.message || "An error occurred during login.";
                        }
                    } else {
                        this.toastColor = "danger";
                        this.messageToaster = "Network error. Please try again.";
                    }

                    this.toasterTimeOut();
                }


            }
            else {
                this.toaster = true;
                this.toastColor = "danger";
                this.messageToaster = "Check Your Fields";
                this.toasterTimeOut();
            }

        },
        clickSignupLink() {
            window.location.href = `/tenantRegister`;

        },

        fill() {
            this.email = "";
            this.password = "";
            this.messageToaste = "";
        },
        loginValidation() {
            this.errors = {};
            if (!this.email) this.errors.email = ['Please enter your email address.'];
            if (!this.password) this.errors.password = ['Please enter your password.'];

            return Object.keys(this.errors).length === 0;

        },
        toggleShowPassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        },
        ExitToaster() {
            this.toaster = false;
            this.messageToaster = "";
        },
        toasterTimeOut() {
            setTimeout(() => {
                this.toaster = false;
                this.toastColor = "";
                this.messageToaster = "";

            }, 5000

            );
        }

    },

}

</script>