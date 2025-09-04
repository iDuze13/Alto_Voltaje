const wrapper = document.querySelector('.wrapper');
const botonPopup = document.querySelector('.BotonLogin');
const botonCerrar = document.querySelector('.cerrar');

const linksLogin = document.querySelectorAll('.link-login');
const linksRegistro = document.querySelectorAll('.link-registro');
const linksEmpleado = document.querySelectorAll('.link-empleado');

// Abrir popup
if (botonPopup) {
  botonPopup.addEventListener('click', () => {
    wrapper.classList.add('active-popup');
    // Por defecto mostramos login
    wrapper.classList.remove('active-registro', 'active-empleado');
  });
}

// Cerrar popup (y volver a login limpio)
if (botonCerrar) {
  botonCerrar.addEventListener('click', () => {
    wrapper.classList.remove('active-popup', 'active-registro', 'active-empleado');
  });
}

// Ir a Registro
linksRegistro.forEach(a => a.addEventListener('click', (e) => {
  e.preventDefault();
  wrapper.classList.add('active-registro'); // active-popup ya está presente
  wrapper.classList.remove('active-empleado');
}));

// Volver a Login
linksLogin.forEach(a => a.addEventListener('click', (e) => {
  e.preventDefault();
  wrapper.classList.remove('active-registro', 'active-empleado');
  // active-popup se mantiene para que el modal siga abierto
}));

// Ir a Empleado
linksEmpleado.forEach(a => a.addEventListener('click', (e) => {
  e.preventDefault();
  wrapper.classList.add('active-empleado');
  wrapper.classList.remove('active-registro');
}));