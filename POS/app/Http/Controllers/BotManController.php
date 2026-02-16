<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle()
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        $config = [
            'web' => [
                'matchingData' => [
                    'driver' => 'web',
                ],
            ]
        ];

        $botman = BotManFactory::create($config);

        // Saludos
        $botman->hears('(hola|buenos dias|buenas tardes|buenas noches)', function (BotMan $bot) {
            $bot->reply('¡Hola! Soy el asistente virtual de Autopartes ALA. ¿En qué puedo ayudarte?');
        });

        // Información de productos
        $botman->hears('(productos|refacciones|catalogo|que venden)', function (BotMan $bot) {
            $bot->reply('En Autopartes ALA ofrecemos una amplia variedad de refacciones automotrices como:
            - Frenos y suspensión
            - Motor y transmisión
            - Sistema eléctrico
            - Carrocería
            ¿Qué tipo de refacción estás buscando?');
        });

        // Información de contacto
        $botman->hears('(contacto|telefono|correo|email|direccion)', function (BotMan $bot) {
            $bot->reply('Puedes contactarnos por estos medios:
            📞 Teléfono: (123) 456-7890
            📧 Email: contacto@autopartesala.com
            📍 Dirección: Calle Principal #123, Ciudad');
        });

        // Horarios
        $botman->hears('(horario|horarios|hora|cuando|dias)', function (BotMan $bot) {
            $bot->reply('Nuestro horario de atención es:
            Lunes a Viernes: 9:00 AM - 6:00 PM
            Sábados: 9:00 AM - 2:00 PM
            Domingos: Cerrado');
        });

        // Métodos de pago
        $botman->hears('(pago|pagos|formas de pago|metodos de pago)', function (BotMan $bot) {
            $bot->reply('Aceptamos los siguientes métodos de pago:
            💳 Tarjetas de crédito y débito
            💵 Efectivo
            🏦 Transferencia bancaria
            📱 Pago móvil');
        });

        // Envíos
        $botman->hears('(envio|envios|entrega|entregas|shipping)', function (BotMan $bot) {
            $bot->reply('Realizamos envíos a toda la República:
            🚚 Envío estándar: 3-5 días hábiles
            ⚡ Envío express: 1-2 días hábiles
            🏠 Envío gratis en compras mayores a $1000');
        });

        // Garantía
        $botman->hears('(garantia|garantias|devolucion|devoluciones)', function (BotMan $bot) {
            $bot->reply('Todas nuestras refacciones cuentan con garantía:
            ✅ 30 días para devoluciones
            🛠️ Garantía de fábrica en productos nuevos
            ⚠️ Consulta términos y condiciones específicos por producto');
        });

        // Despedida
        $botman->hears('(adios|gracias|bye|hasta luego)', function (BotMan $bot) {
            $bot->reply('¡Gracias por contactar a Autopartes ALA! Estamos para servirte. ¡Que tengas un excelente día!');
        });

        // Mensaje por defecto
        $botman->fallback(function (BotMan $bot) {
            $bot->reply('Lo siento, no entiendo tu consulta. ¿Podrías reformularla? Puedes preguntarme sobre:
            - Productos y refacciones
            - Información de contacto
            - Horarios de atención
            - Métodos de pago
            - Envíos y entregas
            - Garantías');
        });

        $botman->listen();
    }
}
