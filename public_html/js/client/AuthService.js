class AuthService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async login(data) {
        try {
            const response = await this.requestClient.post('/login', data);
            Swal.fire('Authenticated..!', response.message, 'success').then(() => {
                this.handleRedirect(response.data.redirect_url);
            });
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async register(data) {
        try {
            const response = await this.requestClient.post('/register', data);
            Swal.fire('Authenticated..!', response.message, 'success').then(() => {
                this.handleRedirect(response.data.redirect_url);
            });
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async setup(data) {
        try {
            const response = await this.requestClient.post('/setup', data);
            Swal.fire('Business Registered..!', response.message, 'success').then(() => {
                this.handleRedirect(response.data.redirect_url);
            });
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async logout(data) {
        try {
            const response = await this.requestClient.post('/logout', data);
            Swal.fire('Logged out!', response.message, 'success').then(() => {
                this.handleRedirect(response.data.redirect_url);
            });
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    handleRedirect(route) {
        if (route) {
            setTimeout(() => {
                window.location.href = route;
            }, 1500);
        }
    }
}

export default AuthService;
