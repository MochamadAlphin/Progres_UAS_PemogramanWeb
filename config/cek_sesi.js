let sessionInterval;

function checkSession() {
    const path = window.location.pathname;
    
    if (path.includes('login.php') || path.includes('register.php')) {
        return; 
    }

    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift().trim();
        return null;
    }

    const sessionActive = getCookie('session_active');

    if (sessionActive === null || sessionActive === undefined || sessionActive === "") {
        
        if (sessionInterval) clearInterval(sessionInterval);

        document.body.style.filter = "blur(10px)";
        document.body.style.pointerEvents = "none";

        Swal.fire({
            icon: 'warning',
            title: 'Sesi Tidak Ditemukan',
            text: 'Silakan login terlebih dahulu untuk mengakses fitur ini.',
            confirmButtonText: 'Ke Halaman Login',
            confirmButtonColor: '#2d2a70',
            allowOutsideClick: false
        }).then((result) => {
            window.location.replace('login.php');
        });
    }
}

window.addEventListener('load', () => {
    setTimeout(() => {
        checkSession();
        sessionInterval = setInterval(checkSession, 5000);
    }, 2000); 
});