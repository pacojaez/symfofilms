<?php

namespace App\Services;

class FrasesService {
     
    public function fraseAleatoria (){

        $frases = [
            "'La música nunca puede ser mala, digan lo que digan del Rock & Roll' Elvis Presley",
            "'Hay quien piensa que si vas muy lejos, no podrás volver donde están los demás' Frank Zappa",
            "'Disculpa que bese el cielo' Jimi Hendrix",
            "'Tu mejor amigo y mi peor enemigo son uno mismo' Bob Dylan",
            "'Que seas un paranoico no quiere decir que no te persigan' Kurt Cobain",
            "'Yo nunca dije la verdad, así que no puedo decir una mentira' Tom Waits",
            "'La mentira acaba siendo verdad' Michael Jackson",
            "'Si no sabes adónde vas, cualquier camino te llevará' George Harrison",
            "'En verdad hay dos sendas que puedes tomar, pero a la larga, siempre estás a tiempo de cambiar de camino' Jimmy Page",
            "'Todo lo que necesitas es amor' John Lennon",
            "'Si no quema, no es arte' Steve Vai",
            "'¿Sabes que me hace más feliz que nada? Dame seis cuerdas y seré feliz' B.B. King",
            "'¿Por qué no nos casamos e intentamos estar solos juntos a ver si somos tan fuertes?' Leonard Cohen",
            "'Tienes que aprender a caer antes de aprender a volar' Paul Simon",
            "'Si el presente es de lucha, el futuro es nuestro' Brian May",
            "'Realmente no importa con qué equipo estás tocando, tu sonido es tu cerebro y tus dedos' Eddie Van Halen",
            "'La imagen es una cosa y el ser humano otra… Es muy difícil vivir como una imagen' Elvis Presley",
            "'Si Elvis libero mi cuerpo,Dylan libero mi mente' Bruce Springsteen",
            "'¿Yo Dios? No, solo soy su mano derecha' Gene Simmons (KISS)",
            "'Lo que te hace sentir bien no te puede causar ningún daño' Janis Joplin",
            "'Hay un hombre de las estrellas esperando en el cielo. Nos ha dicho que no lo hagamos explotar porque sabe que merece la pena' David Bowie",
            "'A través de estos campos de destrucción, bautismos de fuego. He sido testigo de tu sufrimiento a medida que la batalla se intensificaba. Y a pesar de que me hirieron tanto, en el miedo y la alarma, no me abandonasteis mis hermanos de armas' Dire Straits",
            "'Quiero saber… ¿has visto alguna vez la lluvia cayendo en un día soleado?' Creedence clearwater revival",
            "'Cariño tienes que hacerme saber…¿debería quedarme o debería irme? Si dices que eres mía, estaré aquí hasta el final de los tiempos' The Clash",
            "'Es difícil creer que no hay nadie allá afuera, es difícil creer que estoy completamente solo. Al menos tengo su amor; la ciudad, ella me ama, solitaria como yo, juntos lloramos' Red Hot Chili Peppers",
            "'Porque eres un verdadero hijo de la naturaleza, nosotros nacemos, nacemos para ser salvajes, podemos escalar muy alto y no querer morir nunca. ¡Nacidos para ser salvajes!' Steppenwolf",
        ];

        return $frases[array_rand($frases)];

    }
}