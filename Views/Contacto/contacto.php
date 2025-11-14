<?php
    headerTienda($data);
	getModal('modalCarrito', $data);
?>

<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('<?= media() ?>/images/heroTienda.png');">
    <h2 class="ltext-105 cl0 txt-center">
        Contacto
    </h2>
</section>	


<section class="bg0 p-t-104 p-b-116">
    <div class="container">
        <div class="flex-w flex-tr">
            <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                <form>
                    
                    <h4 class="mtext-105 cl2 txt-center p-b-30">
                        Envianos un mensaje
                    </h4>

                    <div class="bor8 m-b-20" style="display: flex; align-items: center; gap: 15px;">
                        <img src="<?= media() ?>/tiendaOnline/images/icons/icon-name.png" alt="ICON" style="width:28px; height:28px; flex-shrink: 0;">
                        <input class="stext-111 cl2 plh3 bor8 p-lr-28 p-tb-15" type="text" name="name" id="nombreContacto" placeholder="Tu nombre completo" style="flex: 1;">
                    </div>

                    <div class="bor8 m-b-20" style="display: flex; align-items: center; gap: 15px;">
                        <img src="<?= media() ?>/tiendaOnline/images/icons/icon-email.png" alt="ICON">
                        <input class="stext-111 cl2 plh3 bor8 p-lr-28 p-tb-15" type="text" name="email" id="emailContacto" placeholder="Tu dirección de correo electrónico" style="flex: 1;">
                    </div>

                    <div class="bor8 m-b-30" style="display: flex; align-items: center; gap: 15px;">
                        <textarea class="stext-111 cl2 plh3 bor8 p-lr-28 p-tb-15" id="mensaje" name="mensaje" rows="5" placeholder="¿Cómo te podemos ayudar?" style= "outline: none; width: 100%; resize: vertical; min-height: 100px; word-wrap: break-word; overflow-wrap: break-word; box-sizing: border-box; white-space: pre-wrap;"></textarea>
                    </div>

                    <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer">
                        Enviar
                    </button>
                </form>
            </div>

            <div class="size-210 bor10 flex-w flex-col-m p-lr-93 p-tb-30 p-lr-15-lg w-full-md">
                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-map-marker"></span>
                    </span>

                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">
                            Dirección
                        </span>

                        <p class="stext-115 cl6 size-213 p-t-18">
                            Frondizi 4566, Bº San Juan Bautista<br>
                            Formosa Capital, CP 3600<br>
                            Argentina
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-phone-handset"></span>
                    </span>

                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">
                            Llamanos
                        </span>

                        <p class="stext-115 cl1 size-213 p-t-18">
                            +54 3704-804704
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-envelope"></span>
                    </span>

                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">
                            Soporte de Ventas
                        </span>

                        <p class="stext-115 cl1 size-213 p-t-18">
                            altovoltaje025@gmail.com
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-clock"></span>
                    </span>

                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">
                            Horarios de Atención
                        </span>

                        <p class="stext-115 cl6 size-220 p-t-18">
                            Lunes a Viernes: 8:15 - 13:30 y 16:15 - 20:30 <br>
                            Sábados: 8:30 - 13:30 y 16:30 - 20:00 <br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>	


<!-- Mapa -->
<div class="map">
    <div class="size-303" id="google_map" data-map-x="-26.1834" data-map-y="-58.1731" data-pin="<?= media() ?>/tiendaOnline/images/icons/pin.png" data-scrollwhell="0" data-draggable="1" data-zoom="15"></div>
</div>

<?php
	footerTienda($data);
?>