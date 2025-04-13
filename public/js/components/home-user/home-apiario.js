/**
 * Dashboard.js - Script para el dashboard de MaiA
 */

document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loader");
    const mainContent = document.getElementById("main-contenload");
    const voiceButton = document.getElementById("voice-button");
    const menuToggle = document.querySelector(".menu-toggle");
    const cards = document.querySelectorAll(".card");

    function showMainContent() {
        if (loader && mainContent) {
            loader.style.opacity = "0";

            setTimeout(() => {
                loader.style.display = "none";
                mainContent.style.display = "block";

                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add("visible");
                    }, 100 * index);
                });
            }, 500);
        }
    }

    setTimeout(showMainContent, 1000);

    cards.forEach((card) => {
        card.addEventListener("mouseenter", function () {
            this.classList.add("card-hover");
            createParticleEffect(this);
        });

        card.addEventListener("mouseleave", function () {
            this.classList.remove("card-hover");
        });
    });

    function createParticleEffect(element) {
        console.log("Efecto visual aplicado a:", element);
    }

    if (voiceButton) {
        voiceButton.addEventListener("click", function () {
            this.classList.add("active");

            setTimeout(() => {
                this.classList.remove("active");
            }, 600);

            activateVoiceAssistant();
        });
    }

    function activateVoiceAssistant() {
        const userData = {
            name: typeof usuario !== "undefined" ? usuario.name : "Usuario",
            tareas_pendientes:
                typeof tareas_pendientes !== "undefined"
                    ? tareas_pendientes
                    : 0,
            tareas_urgentes:
                typeof tareas_urgentes !== "undefined" ? tareas_urgentes : 0,
            tareas_progreso:
                typeof tareas_progreso !== "undefined" ? tareas_progreso : 0,
        };

        const nombreDePila = obtenerNombreDePila(userData.name);

        const mensajes = [
            `Bienvenido otra vez ${nombreDePila}, veo que tienes ${userData.tareas_pendientes} tareas sin realizar.`,
            `Hola ${nombreDePila}, tienes ${userData.tareas_urgentes} tareas urgentes que requieren tu atención.`,
            `¡Saludos! ${nombreDePila} Actualmente tienes ${userData.tareas_progreso} tareas en progreso.`,
            `${nombreDePila} Es posible que tengas tareas pendientes, revisa tu lista en el apartado para estar al día.`,
        ];

        const mensajeAleatorio =
            mensajes[Math.floor(Math.random() * mensajes.length)];
        if (typeof VoiceReader !== "undefined" && VoiceReader.readText) {
            VoiceReader.readText(mensajeAleatorio);
        } else {
            console.log("VoiceReader no está disponible:", mensajeAleatorio);
        }
    }

    function obtenerNombreDePila(nombreCompleto) {
        if (!nombreCompleto || typeof nombreCompleto !== "string") {
            return "Usuario";
        }
        return nombreCompleto.split(" ")[0];
    }

    if (menuToggle) {
        menuToggle.addEventListener("click", function () {
            this.classList.toggle("active");
        });
    }

    initTour();
});

function initTour() {
    if (typeof Shepherd !== "undefined") {
        const tour = new Shepherd.Tour({
            defaultStepOptions: {
                classes: "shepherd-theme-custom",
                scrollTo: true,
                cancelIcon: {
                    enabled: true,
                },
            },
            useModalOverlay: true,
        });

        const steps = [
            {
                element: ".card-apiarios",
                title: "Apiarios",
                text: "Aquí puedes gestionar todos tus apiarios, revisar el total de colmenas y más detalles.",
                position: "bottom",
            },
            {
                element: ".card-inspecciones",
                title: "Inspecciones",
                text: "Accede al cuaderno de campo para realizar inspecciones. Revisa los detalles y organiza tu temporada.",
                position: "bottom",
            },
            {
                element: ".card-tareas",
                title: "Tareas",
                text: "Aquí puedes gestionar tu agenda.",
                position: "bottom",
            },
            {
                element: ".card-zonificacion",
                title: "Zonificación",
                text: "Gestiona áreas geográficas relacionadas con tus apiarios.",
                position: "bottom",
            },
            {
                element: ".voice-button",
                title: "Asistente de Voz",
                text: "Haz clic aquí para activar el asistente de voz que te dará información relevante.",
                position: "left",
            },
        ];

        steps.forEach((step) => {
            tour.addStep({
                text: step.text,
                attachTo: {
                    element: step.element,
                    on: step.position || "bottom",
                },
                title: step.title,
                buttons: [
                    {
                        text: "Siguiente",
                        action: tour.next,
                    },
                    {
                        text: "Cancelar",
                        action: tour.cancel,
                    },
                ],
            });
        });
        if (!localStorage.getItem("hasSeenTour")) {
            tour.start();
            localStorage.setItem("hasSeenTour", true);
        }
    }
}

class ParticleEffect {
    constructor(element) {
        this.element = element;
        this.canvas = document.createElement("canvas");
        this.ctx = this.canvas.getContext("2d");
        this.particles = [];
        this.init();
    }

    init() {
        this.element.appendChild(this.canvas);
        this.resize();
        window.addEventListener("resize", () => this.resize());

        this.createParticles();
        this.animate();
    }

    resize() {
        this.canvas.width = this.element.offsetWidth;
        this.canvas.height = this.element.offsetHeight;
    }

    createParticles() {
        const particleCount = 20;
        for (let i = 0; i < particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                radius: Math.random() * 3 + 1,
                color: this.getParticleColor(),
                speed: Math.random() * 1 + 0.5,
                direction: Math.random() * Math.PI * 2,
            });
        }
    }

    getParticleColor() {
        if (this.element.classList.contains("card-apiarios")) {
            return "rgba(127, 204, 222, 0.7)";
        } else if (this.element.classList.contains("card-inspecciones")) {
            return "rgba(248, 194, 145, 0.7)";
        } else if (this.element.classList.contains("card-tareas")) {
            return "rgba(255, 255, 255, 0.7)";
        } else if (this.element.classList.contains("card-zonificacion")) {
            return "rgba(84, 109, 229, 0.7)";
        }
        return "rgba(230, 126, 34, 0.7)";
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        this.particles.forEach((particle) => {
            particle.x += Math.cos(particle.direction) * particle.speed;
            particle.y += Math.sin(particle.direction) * particle.speed;

            if (particle.x < 0 || particle.x > this.canvas.width) {
                particle.direction = Math.PI - particle.direction;
            }
            if (particle.y < 0 || particle.y > this.canvas.height) {
                particle.direction = -particle.direction;
            }

            this.ctx.beginPath();
            this.ctx.arc(
                particle.x,
                particle.y,
                particle.radius,
                0,
                Math.PI * 2
            );
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();
        });
        requestAnimationFrame(() => this.animate());
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".card");
    cards.forEach((card) => new ParticleEffect(card));
});
