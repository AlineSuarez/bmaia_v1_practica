// Asumiendo axios y que el usuario ya está autenticado en sesión
async function loadPermissions() {
    const { data } = await axios.get('/api/permissions');
    // inicializa tus switches con data.notifications, data.camera_access, etc.
}
async function savePermissions(state) {
    // state = { notifications: true, camera_access: false, … }
    const { data } = await axios.post('/api/permissions', state);
    alert(data.message);
}

async function resetPermissions() {
    const { data } = await axios.post('/api/permissions/reset');
    alert(data.message);
    // y recarga los switches con data.permissions
}