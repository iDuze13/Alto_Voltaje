<?php
    headerTienda($data);
	getModal('modalCarrito', $data);
?>

<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('<?= media() ?>/images/heroTienda.png');">
    <h2 class="ltext-105 cl0 txt-center">
        Sobre Nosotros
    </h2>
</section>	



<section class="bg0 p-t-75 p-b-120">
    <div class="container">
        <div class="row p-b-148">
            <div class="col-md-7 col-lg-8">
                <div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        Quiénes Somos
                    </h3>

                    <p class="stext-113 cl6 p-b-26">
                        En Alto Voltaje somos un comercio de venta de materiales eléctricos ubicado en la ciudad de Formosa Capital. Nuestro proyecto nació en plena pandemia, a fines de 2020, con el propósito de ofrecer productos eléctricos accesibles y de buena calidad para nuestro barrio en un momento difícil para todos.
                    </p>

                    <p class="stext-113 cl6 p-b-26">
                        Hoy contamos con un equipo de personas comprometidas que nos acompañan cada día. Nos destacamos por dos pilares fundamentales: la gran variedad de materiales eléctricos que ofrecemos y la atención personalizada que reciben todos los que nos visitan.
                    </p>

                    <p class="stext-113 cl6 p-b-26">
                        ¿Tienes alguna pregunta? Visítanos en nuestro local Frondizi 4566, Bº San Juan Bautista, Formosa Capital o llámanos al (+54) 3704-804704.
                    </p>
                </div>
            </div>

            <div class="col-11 col-md-5 col-lg-4 m-lr-auto">
                <div class="how-bor1 ">
                    <div class="hov-img0">
                        <img src="<?= media() ?>/tiendaOnline/images/about-01.jpg" alt="IMG">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="order-md-2 col-md-7 col-lg-8 p-b-30">
                <div class="p-t-7 p-l-85 p-l-15-lg p-l-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        Nuestra Misión
                    </h3>

                    <p class="stext-113 cl6 p-b-26">
                        Nos dedicamos a brindar soluciones eléctricas confiables y accesibles para hogares y empresas. Nuestro compromiso es ofrecer productos de calidad respaldados por un servicio excepcional, contribuyendo al desarrollo y bienestar de nuestra comunidad en Formosa.
                    </p>

                    <p class="stext-113 cl6 p-b-26">
                        Creemos que cada cliente merece atención personalizada y productos que cumplan con los más altos estándares de seguridad y durabilidad. Por eso trabajamos constantemente para mantener un inventario actualizado y un equipo capacitado para asesorar en cada compra.
                    </p>
                </div>
            </div>

            <div class="order-md-1 col-11 col-md-5 col-lg-4 m-lr-auto p-b-30">
                <div class="how-bor2">
                    <div class="hov-img0">
                        <img src="<?= media() ?>/tiendaOnline/images/about-02.jpg" alt="IMG">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Sección de Valores con fondo gris -->
<section class="services-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="txt-center p-b-30">
                    <h3 class="mtext-111 cl2 p-b-16">
                        ¿Por qué elegir Alto Voltaje?
                    </h3>
                    <p class="stext-113 cl6">
                        Conoce los valores que nos distinguen en el mercado eléctrico
                    </p>
                </div>
                
                <div class="team-grid">
                    <div class="team-member">
                        <div class="service-icon">⚡</div>
                        <h3>Experiencia</h3>
                        <p>Con más de 5 años en el mercado nuestro propósito es ofrecer productos eléctricos accesibles y de buena calidad.</p>
                    </div>
                    <div class="team-member">
                        <div class="service-icon">🛡️</div>
                        <h3>Seguridad</h3>
                        <p>Contamos con productos originales y trabajamos con garantías y un servicio de atención al cliente para resolver todas sus dudas.</p>
                    </div>
                    <div class="team-member">
                        <div class="service-icon">👥</div>
                        <h3>Profesionales</h3>
                        <p>Contamos con un equipo a su disposición para ayudarlo en lo que necesite.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

 <!-- Sección Servicios con fondo blanco -->
<section id="servicios" class="section">
    <div class="container">
        <div class="txt-center p-b-30">
            <h2>Nuestros Servicios</h2>
            <p>
                En Alto Voltaje trabajamos día a día para ofrecer soluciones completas en materiales eléctricos, pensando siempre en la calidad y en el bolsillo de nuestros clientes.
            </p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🔌</div>
                <h3>Venta de materiales eléctricos</h3>
                <p>Contamos con una amplia variedad de productos, desde lo más básico hasta opciones más específicas, para hogares, comercios y obras.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🤝</div>
                <h3>Asesoramiento personalizado</h3>
                <p>Sabemos que cada necesidad es distinta. Por eso, acompañamos a nuestros clientes en la elección de los materiales adecuados, brindando atención cercana y confiable.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🕑​</div>
                <h3>Disponibilidad y accesibilidad</h3>
                <p>Nos esforzamos por mantener siempre stock actualizado y precios accesibles, asegurando que encuentres lo que buscás sin complicaciones.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">✨</div>
                <h3>Compromiso con la calidad</h3>
                <p>Todos nuestros productos cumplen con altos estándares de seguridad y durabilidad, porque nuestra misión es garantizar que cada compra sea una inversión segura.</p>
            </div>
        </div>
    </div>
</section>

<?php
	footerTienda($data);
?>