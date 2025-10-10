<?php
/**
 * Plugin Name: WP Associates
 * Description: Plugin para registrar associados com nome, localização, imagem e filtros interativos com mapa.
 * Version: 2.3
 * Author: Henrique Costa
 */

if (!defined('ABSPATH')) exit;

/**
 * 0) Lista de municípios da Bahia com coordenadas
 */
function associates_get_bahia_municipalities() {
    return array(
        'Abaíra' => array('lat' => -13.2500, 'lng' => -41.6667),
        'Abaré' => array('lat' => -8.7228, 'lng' => -39.1156),
        'Acajutiba' => array('lat' => -11.6594, 'lng' => -38.0178),
        'Adustina' => array('lat' => -10.5394, 'lng' => -38.1028),
        'Água Fria' => array('lat' => -11.9606, 'lng' => -39.0311),
        'Aiquara' => array('lat' => -14.1417, 'lng' => -39.8714),
        'Alagoinhas' => array('lat' => -12.1361, 'lng' => -38.4189),
        'Alcobaça' => array('lat' => -17.5208, 'lng' => -39.2000),
        'Almadina' => array('lat' => -14.7089, 'lng' => -39.6422),
        'Amargosa' => array('lat' => -13.0267, 'lng' => -39.6050),
        'Amélia Rodrigues' => array('lat' => -12.3486, 'lng' => -38.7647),
        'América Dourada' => array('lat' => -11.4550, 'lng' => -41.4372),
        'Anagé' => array('lat' => -14.6181, 'lng' => -41.1325),
        'Andaraí' => array('lat' => -12.8050, 'lng' => -41.3322),
        'Andorinha' => array('lat' => -10.3472, 'lng' => -39.8908),
        'Angical' => array('lat' => -12.0106, 'lng' => -44.6456),
        'Anguera' => array('lat' => -12.1481, 'lng' => -39.2450),
        'Antas' => array('lat' => -10.3558, 'lng' => -38.3308),
        'Antônio Cardoso' => array('lat' => -12.4322, 'lng' => -39.1214),
        'Antônio Gonçalves' => array('lat' => -10.5806, 'lng' => -40.3083),
        'Aporá' => array('lat' => -11.6389, 'lng' => -38.1556),
        'Apuarema' => array('lat' => -13.8536, 'lng' => -39.7447),
        'Araçás' => array('lat' => -12.2203, 'lng' => -38.1483),
        'Aracatu' => array('lat' => -14.4294, 'lng' => -41.4581),
        'Araci' => array('lat' => -11.3297, 'lng' => -38.9578),
        'Aramari' => array('lat' => -12.0881, 'lng' => -38.4994),
        'Arataca' => array('lat' => -15.2661, 'lng' => -39.4172),
        'Aratuípe' => array('lat' => -13.0797, 'lng' => -38.9342),
        'Aurelino Leal' => array('lat' => -14.3222, 'lng' => -39.3525),
        'Baianópolis' => array('lat' => -12.3050, 'lng' => -44.5508),
        'Baixa Grande' => array('lat' => -11.9508, 'lng' => -40.1697),
        'Banzaê' => array('lat' => -10.5803, 'lng' => -38.6153),
        'Barra' => array('lat' => -11.0889, 'lng' => -43.1417),
        'Barra da Estiva' => array('lat' => -13.6258, 'lng' => -41.3275),
        'Barra do Choça' => array('lat' => -14.8647, 'lng' => -40.5828),
        'Barra do Mendes' => array('lat' => -11.8058, 'lng' => -42.0603),
        'Barra do Rocha' => array('lat' => -13.9811, 'lng' => -39.6169),
        'Barreiras' => array('lat' => -12.1528, 'lng' => -44.9900),
        'Barro Alto' => array('lat' => -11.7506, 'lng' => -41.9189),
        'Barrocas' => array('lat' => -11.5294, 'lng' => -39.0794),
        'Barro Preto' => array('lat' => -14.7894, 'lng' => -39.4464),
        'Belmonte' => array('lat' => -15.8631, 'lng' => -38.8822),
        'Belo Campo' => array('lat' => -14.9778, 'lng' => -41.2592),
        'Biritinga' => array('lat' => -11.6119, 'lng' => -38.8042),
        'Boa Nova' => array('lat' => -14.3611, 'lng' => -40.2078),
        'Boa Vista do Tupim' => array('lat' => -12.6722, 'lng' => -40.6164),
        'Bom Jesus da Lapa' => array('lat' => -13.2550, 'lng' => -43.4181),
        'Bom Jesus da Serra' => array('lat' => -14.3714, 'lng' => -40.5117),
        'Boninal' => array('lat' => -12.7025, 'lng' => -41.8319),
        'Bonito' => array('lat' => -11.9650, 'lng' => -41.2631),
        'Boquira' => array('lat' => -12.8183, 'lng' => -42.7314),
        'Botuporã' => array('lat' => -13.3378, 'lng' => -42.5247),
        'Brejões' => array('lat' => -13.1031, 'lng' => -39.7931),
        'Brejolândia' => array('lat' => -12.4889, 'lng' => -43.9542),
        'Brotas de Macaúbas' => array('lat' => -12.0153, 'lng' => -42.6281),
        'Brumado' => array('lat' => -14.2036, 'lng' => -41.6653),
        'Buerarema' => array('lat' => -14.9586, 'lng' => -39.2953),
        'Buritirama' => array('lat' => -10.7286, 'lng' => -43.6442),
        'Caatiba' => array('lat' => -14.9700, 'lng' => -40.4092),
        'Cabaceiras do Paraguaçu' => array('lat' => -12.5361, 'lng' => -39.1753),
        'Cachoeira' => array('lat' => -12.5947, 'lng' => -38.9572),
        'Caculé' => array('lat' => -14.5036, 'lng' => -42.2231),
        'Caém' => array('lat' => -11.0597, 'lng' => -40.4331),
        'Caetanos' => array('lat' => -14.3511, 'lng' => -40.9264),
        'Caetité' => array('lat' => -14.0669, 'lng' => -42.4817),
        'Cafarnaum' => array('lat' => -11.6889, 'lng' => -41.4714),
        'Cairu' => array('lat' => -13.4894, 'lng' => -39.0422),
        'Caldeirão Grande' => array('lat' => -10.9947, 'lng' => -40.3131),
        'Camacan' => array('lat' => -15.4167, 'lng' => -39.4928),
        'Camaçari' => array('lat' => -12.6975, 'lng' => -38.3244),
        'Camamu' => array('lat' => -13.9439, 'lng' => -39.1017),
        'Campo Alegre de Lourdes' => array('lat' => -9.5419, 'lng' => -43.0161),
        'Campo Formoso' => array('lat' => -10.5075, 'lng' => -40.3219),
        'Canápolis' => array('lat' => -13.0050, 'lng' => -44.2081),
        'Canarana' => array('lat' => -11.6858, 'lng' => -41.7653),
        'Canavieiras' => array('lat' => -15.6756, 'lng' => -38.9472),
        'Candeal' => array('lat' => -11.8111, 'lng' => -39.1181),
        'Candeias' => array('lat' => -12.6706, 'lng' => -38.5478),
        'Candiba' => array('lat' => -14.4103, 'lng' => -42.8681),
        'Cândido Sales' => array('lat' => -15.5153, 'lng' => -41.2397),
        'Cansanção' => array('lat' => -10.6669, 'lng' => -39.5008),
        'Canudos' => array('lat' => -9.9014, 'lng' => -39.0086),
        'Capela do Alto Alegre' => array('lat' => -11.5886, 'lng' => -39.8525),
        'Capim Grosso' => array('lat' => -11.3819, 'lng' => -40.0147),
        'Caraíbas' => array('lat' => -14.6264, 'lng' => -41.2856),
        'Caravelas' => array('lat' => -17.7325, 'lng' => -39.2481),
        'Cardeal da Silva' => array('lat' => -11.9511, 'lng' => -37.9528),
        'Carinhanha' => array('lat' => -14.3028, 'lng' => -43.7658),
        'Casa Nova' => array('lat' => -9.1639, 'lng' => -40.9739),
        'Castro Alves' => array('lat' => -12.7619, 'lng' => -39.4264),
        'Catolândia' => array('lat' => -12.3403, 'lng' => -44.7600),
        'Catu' => array('lat' => -12.3528, 'lng' => -38.3789),
        'Caturama' => array('lat' => -13.3117, 'lng' => -42.2906),
        'Central' => array('lat' => -11.1353, 'lng' => -42.1169),
        'Chorrochó' => array('lat' => -8.9944, 'lng' => -39.1372),
        'Cícero Dantas' => array('lat' => -10.5986, 'lng' => -38.3825),
        'Cipó' => array('lat' => -11.0953, 'lng' => -38.5089),
        'Coaraci' => array('lat' => -14.6408, 'lng' => -39.5522),
        'Cocos' => array('lat' => -14.1853, 'lng' => -44.5325),
        'Conceição da Feira' => array('lat' => -12.5072, 'lng' => -38.9972),
        'Conceição do Almeida' => array('lat' => -12.7781, 'lng' => -39.1722),
        'Conceição do Coité' => array('lat' => -11.5650, 'lng' => -39.2831),
        'Conceição do Jacuípe' => array('lat' => -12.3194, 'lng' => -38.7636),
        'Conde' => array('lat' => -11.8136, 'lng' => -37.6106),
        'Condeúba' => array('lat' => -14.8997, 'lng' => -41.9714),
        'Contendas do Sincorá' => array('lat' => -13.7550, 'lng' => -41.0431),
        'Coração de Maria' => array('lat' => -12.2322, 'lng' => -38.7511),
        'Cordeiros' => array('lat' => -13.4278, 'lng' => -41.9314),
        'Coribe' => array('lat' => -13.8306, 'lng' => -44.4456),
        'Coronel João Sá' => array('lat' => -10.2881, 'lng' => -37.9892),
        'Correntina' => array('lat' => -13.3428, 'lng' => -44.6372),
        'Cotegipe' => array('lat' => -12.0264, 'lng' => -44.2522),
        'Cravolândia' => array('lat' => -13.3544, 'lng' => -39.8086),
        'Crisópolis' => array('lat' => -11.5147, 'lng' => -38.1553),
        'Cristópolis' => array('lat' => -12.2267, 'lng' => -44.4264),
        'Cruz das Almas' => array('lat' => -12.6706, 'lng' => -39.1067),
        'Curaçá' => array('lat' => -8.9914, 'lng' => -39.9069),
        'Dário Meira' => array('lat' => -14.4417, 'lng' => -39.9261),
        'Dias d\'Ávila' => array('lat' => -12.6139, 'lng' => -38.2911),
        'Dom Basílio' => array('lat' => -13.7600, 'lng' => -41.7989),
        'Dom Macedo Costa' => array('lat' => -12.8814, 'lng' => -39.1778),
        'Elísio Medrado' => array('lat' => -12.9681, 'lng' => -39.5467),
        'Encruzilhada' => array('lat' => -15.5286, 'lng' => -40.9381),
        'Entre Rios' => array('lat' => -11.9419, 'lng' => -38.0856),
        'Érico Cardoso' => array('lat' => -13.4344, 'lng' => -42.1308),
        'Esplanada' => array('lat' => -11.7958, 'lng' => -37.9453),
        'Euclides da Cunha' => array('lat' => -10.5081, 'lng' => -39.0147),
        'Eunápolis' => array('lat' => -16.3769, 'lng' => -39.5828),
        'Fátima' => array('lat' => -10.6689, 'lng' => -38.1842),
        'Feira da Mata' => array('lat' => -14.2383, 'lng' => -44.2019),
        'Feira de Santana' => array('lat' => -12.2664, 'lng' => -38.9658),
        'Filadélfia' => array('lat' => -10.7386, 'lng' => -40.1456),
        'Firmino Alves' => array('lat' => -14.9869, 'lng' => -39.9539),
        'Floresta Azul' => array('lat' => -14.8569, 'lng' => -39.6667),
        'Formosa do Rio Preto' => array('lat' => -11.0469, 'lng' => -45.1933),
        'Gandu' => array('lat' => -13.7456, 'lng' => -39.4875),
        'Gavião' => array('lat' => -11.4544, 'lng' => -39.8478),
        'Gentio do Ouro' => array('lat' => -11.4331, 'lng' => -42.5078),
        'Glória' => array('lat' => -9.3417, 'lng' => -38.2628),
        'Gongogi' => array('lat' => -14.3253, 'lng' => -39.4661),
        'Governador Mangabeira' => array('lat' => -12.6028, 'lng' => -39.0389),
        'Guajeru' => array('lat' => -14.5636, 'lng' => -41.9053),
        'Guanambi' => array('lat' => -14.2231, 'lng' => -42.7817),
        'Guaratinga' => array('lat' => -16.5864, 'lng' => -39.7847),
        'Heliópolis' => array('lat' => -10.6836, 'lng' => -38.2861),
        'Iaçu' => array('lat' => -12.7647, 'lng' => -40.2128),
        'Ibiassucê' => array('lat' => -14.7783, 'lng' => -41.2803),
        'Ibicaraí' => array('lat' => -14.8631, 'lng' => -39.5853),
        'Ibicoara' => array('lat' => -13.4078, 'lng' => -41.2847),
        'Ibicuí' => array('lat' => -14.8475, 'lng' => -39.9872),
        'Ibipeba' => array('lat' => -11.6369, 'lng' => -42.0097),
        'Ibipitanga' => array('lat' => -12.8814, 'lng' => -42.4819),
        'Ibiquera' => array('lat' => -12.6611, 'lng' => -40.9156),
        'Ibirapitanga' => array('lat' => -14.1664, 'lng' => -39.3814),
        'Ibirapuã' => array('lat' => -17.6803, 'lng' => -40.1178),
        'Ibirataia' => array('lat' => -14.0631, 'lng' => -39.6431),
        'Ibitiara' => array('lat' => -12.6311, 'lng' => -42.2194),
        'Ibititá' => array('lat' => -11.5692, 'lng' => -41.9808),
        'Ibotirama' => array('lat' => -12.1839, 'lng' => -43.2208),
        'Ichu' => array('lat' => -11.7478, 'lng' => -39.1925),
        'Igaporã' => array('lat' => -13.7678, 'lng' => -42.7197),
        'Igrapiúna' => array('lat' => -13.8278, 'lng' => -39.1492),
        'Iguaí' => array('lat' => -14.7528, 'lng' => -40.0914),
        'Ilhéus' => array('lat' => -14.7897, 'lng' => -39.0494),
        'Inhambupe' => array('lat' => -11.7833, 'lng' => -38.3569),
        'Ipecaetá' => array('lat' => -12.3050, 'lng' => -39.2989),
        'Ipiaú' => array('lat' => -14.1361, 'lng' => -39.7328),
        'Ipirá' => array('lat' => -12.1578, 'lng' => -39.7372),
        'Ipupiara' => array('lat' => -11.8150, 'lng' => -42.6200),
        'Irajuba' => array('lat' => -13.2619, 'lng' => -40.0431),
        'Iramaia' => array('lat' => -13.2958, 'lng' => -40.9639),
        'Iraquara' => array('lat' => -12.2461, 'lng' => -41.6164),
        'Irará' => array('lat' => -12.0525, 'lng' => -38.7653),
        'Irecê' => array('lat' => -11.3042, 'lng' => -41.8556),
        'Itabela' => array('lat' => -16.5742, 'lng' => -39.5581),
        'Itaberaba' => array('lat' => -12.5269, 'lng' => -40.3069),
        'Itabuna' => array('lat' => -14.7858, 'lng' => -39.2803),
        'Itacaré' => array('lat' => -14.2758, 'lng' => -38.9961),
        'Itaeté' => array('lat' => -12.9881, 'lng' => -40.9711),
        'Itagi' => array('lat' => -14.1689, 'lng' => -40.0089),
        'Itagibá' => array('lat' => -14.2753, 'lng' => -39.8522),
        'Itagimirim' => array('lat' => -16.0092, 'lng' => -39.6317),
        'Itaguaçu da Bahia' => array('lat' => -11.0028, 'lng' => -42.4342),
        'Itaju do Colônia' => array('lat' => -15.1347, 'lng' => -39.7192),
        'Itajuípe' => array('lat' => -14.6753, 'lng' => -39.3689),
        'Itamaraju' => array('lat' => -17.0392, 'lng' => -39.5308),
        'Itamari' => array('lat' => -13.7789, 'lng' => -39.6636),
        'Itambé' => array('lat' => -15.2428, 'lng' => -40.6192),
        'Itanagra' => array('lat' => -12.2661, 'lng' => -38.0203),
        'Itanhém' => array('lat' => -17.1539, 'lng' => -40.3214),
        'Itaparica' => array('lat' => -12.8858, 'lng' => -38.6833),
        'Itapé' => array('lat' => -14.8947, 'lng' => -39.4150),
        'Itapebi' => array('lat' => -15.9589, 'lng' => -39.5372),
        'Itapetinga' => array('lat' => -15.2481, 'lng' => -40.2481),
        'Itapicuru' => array('lat' => -11.3119, 'lng' => -38.2281),
        'Itapitanga' => array('lat' => -14.4247, 'lng' => -39.5936),
        'Itaquara' => array('lat' => -13.4431, 'lng' => -39.9286),
        'Itarantim' => array('lat' => -15.6578, 'lng' => -40.0658),
        'Itatim' => array('lat' => -12.7089, 'lng' => -39.6972),
        'Itiruçu' => array('lat' => -13.5297, 'lng' => -40.1489),
        'Itiúba' => array('lat' => -10.7086, 'lng' => -39.8481),
        'Itororó' => array('lat' => -15.1189, 'lng' => -40.0758),
        'Ituaçu' => array('lat' => -13.8106, 'lng' => -41.2981),
        'Ituberá' => array('lat' => -13.7328, 'lng' => -39.1481),
        'Iuiú' => array('lat' => -14.3947, 'lng' => -43.5644),
        'Jaborandi' => array('lat' => -13.6700, 'lng' => -44.1653),
        'Jacaraci' => array('lat' => -14.8486, 'lng' => -42.4314),
        'Jacobina' => array('lat' => -11.1856, 'lng' => -40.5078),
        'Jaguaquara' => array('lat' => -13.5311, 'lng' => -39.9700),
        'Jaguarari' => array('lat' => -10.2614, 'lng' => -40.1978),
        'Jaguaripe' => array('lat' => -13.1100, 'lng' => -38.8989),
        'Jandaíra' => array('lat' => -11.5486, 'lng' => -37.7833),
        'Jequié' => array('lat' => -13.8578, 'lng' => -40.0839),
        'Jeremoabo' => array('lat' => -10.0758, 'lng' => -38.3511),
        'Jiquiriçá' => array('lat' => -13.2611, 'lng' => -39.5800),
        'Jitaúna' => array('lat' => -14.0161, 'lng' => -39.8925),
        'João Dourado' => array('lat' => -11.3508, 'lng' => -41.6606),
        'Juazeiro' => array('lat' => -9.4114, 'lng' => -40.5028),
        'Jucuruçu' => array('lat' => -16.8569, 'lng' => -40.1706),
        'Jussara' => array('lat' => -11.0414, 'lng' => -41.9731),
        'Jussari' => array('lat' => -15.1797, 'lng' => -39.4833),
        'Jussiape' => array('lat' => -13.5289, 'lng' => -41.5928),
        'Lafaiete Coutinho' => array('lat' => -13.6097, 'lng' => -40.2531),
        'Lagoa Real' => array('lat' => -14.0372, 'lng' => -42.1328),
        'Laje' => array('lat' => -13.1711, 'lng' => -39.4261),
        'Lajedão' => array('lat' => -17.4617, 'lng' => -40.3550),
        'Lajedinho' => array('lat' => -12.3678, 'lng' => -40.9239),
        'Lajedo do Tabocal' => array('lat' => -13.4511, 'lng' => -40.1489),
        'Lamarão' => array('lat' => -11.7700, 'lng' => -38.8808),
        'Lapão' => array('lat' => -11.3867, 'lng' => -41.8364),
        'Lauro de Freitas' => array('lat' => -12.8944, 'lng' => -38.3228),
        'Lençóis' => array('lat' => -12.5589, 'lng' => -41.3869),
        'Licínio de Almeida' => array('lat' => -14.6847, 'lng' => -42.5053),
        'Livramento de Nossa Senhora' => array('lat' => -13.6356, 'lng' => -41.8414),
        'Luís Eduardo Magalhães' => array('lat' => -12.0978, 'lng' => -45.7914),
        'Macajuba' => array('lat' => -12.1408, 'lng' => -40.3572),
        'Macarani' => array('lat' => -15.5633, 'lng' => -40.4211),
        'Macaúbas' => array('lat' => -13.0169, 'lng' => -42.6978),
        'Macururé' => array('lat' => -9.1808, 'lng' => -39.0300),
        'Madre de Deus' => array('lat' => -12.7439, 'lng' => -38.6189),
        'Maetinga' => array('lat' => -14.6903, 'lng' => -41.4778),
        'Maiquinique' => array('lat' => -15.6133, 'lng' => -40.2550),
        'Mairi' => array('lat' => -11.7142, 'lng' => -40.1389),
        'Malhada' => array('lat' => -14.3397, 'lng' => -43.7694),
        'Malhada de Pedras' => array('lat' => -14.3864, 'lng' => -41.8856),
        'Manoel Vitorino' => array('lat' => -14.1497, 'lng' => -40.2336),
        'Mansidão' => array('lat' => -10.7364, 'lng' => -44.0472),
        'Maracás' => array('lat' => -13.4439, 'lng' => -40.4278),
        'Maragogipe' => array('lat' => -12.7781, 'lng' => -38.9192),
        'Maraú' => array('lat' => -14.1025, 'lng' => -39.0111),
        'Marcionílio Souza' => array('lat' => -13.0097, 'lng' => -40.5278),
        'Mascote' => array('lat' => -15.5631, 'lng' => -39.3036),
        'Mata de São João' => array('lat' => -12.5281, 'lng' => -38.2992),
        'Matina' => array('lat' => -13.9714, 'lng' => -42.3850),
        'Medeiros Neto' => array('lat' => -17.3736, 'lng' => -40.2214),
        'Miguel Calmon' => array('lat' => -11.4303, 'lng' => -40.5978),
        'Milagres' => array('lat' => -12.8717, 'lng' => -39.8531),
        'Mirangaba' => array('lat' => -10.9214, 'lng' => -40.5817),
        'Mirante' => array('lat' => -14.2603, 'lng' => -40.7719),
        'Monte Santo' => array('lat' => -10.4381, 'lng' => -39.3328),
        'Morpará' => array('lat' => -11.5558, 'lng' => -43.2847),
        'Morro do Chapéu' => array('lat' => -11.5508, 'lng' => -41.1608),
        'Mortugaba' => array('lat' => -15.0008, 'lng' => -42.3650),
        'Mucugê' => array('lat' => -13.0050, 'lng' => -41.3708),
        'Mucuri' => array('lat' => -18.0867, 'lng' => -39.5508),
        'Mulungu do Morro' => array('lat' => -11.9764, 'lng' => -42.1569),
        'Mundo Novo' => array('lat' => -11.8569, 'lng' => -40.4711),
        'Muniz Ferreira' => array('lat' => -13.0167, 'lng' => -39.1092),
        'Muquém de São Francisco' => array('lat' => -12.0739, 'lng' => -43.5481),
        'Muritiba' => array('lat' => -12.6261, 'lng' => -39.1039),
        'Mutuípe' => array('lat' => -13.2378, 'lng' => -39.5069),
        'Nazaré' => array('lat' => -12.9700, 'lng' => -39.0131),
        'Nilo Peçanha' => array('lat' => -13.6206, 'lng' => -39.0953),
        'Nordestina' => array('lat' => -10.8200, 'lng' => -39.4222),
        'Nova Canaã' => array('lat' => -14.7972, 'lng' => -40.1431),
        'Nova Fátima' => array('lat' => -11.6161, 'lng' => -39.6300),
        'Nova Ibiá' => array('lat' => -13.7953, 'lng' => -39.6878),
        'Nova Itarana' => array('lat' => -13.0069, 'lng' => -40.0956),
        'Nova Redenção' => array('lat' => -12.8111, 'lng' => -41.0739),
        'Nova Soure' => array('lat' => -11.2372, 'lng' => -38.4839),
        'Nova Viçosa' => array('lat' => -17.8917, 'lng' => -39.3717),
        'Novo Horizonte' => array('lat' => -12.8097, 'lng' => -42.1692),
        'Novo Triunfo' => array('lat' => -10.3133, 'lng' => -38.4150),
        'Olindina' => array('lat' => -11.3747, 'lng' => -38.3431),
        'Oliveira dos Brejinhos' => array('lat' => -12.3164, 'lng' => -42.8969),
        'Ouriçangas' => array('lat' => -12.0206, 'lng' => -38.6219),
        'Ourolândia' => array('lat' => -10.9558, 'lng' => -41.0600),
        'Palmas de Monte Alto' => array('lat' => -14.2686, 'lng' => -43.1608),
        'Palmeiras' => array('lat' => -12.5078, 'lng' => -41.5803),
        'Paramirim' => array('lat' => -13.4428, 'lng' => -42.2397),
        'Paratinga' => array('lat' => -12.6914, 'lng' => -43.1836),
        'Paripiranga' => array('lat' => -10.6869, 'lng' => -37.8606),
        'Pau Brasil' => array('lat' => -15.4581, 'lng' => -39.6517),
        'Paulo Afonso' => array('lat' => -9.4014, 'lng' => -38.2169),
        'Pé de Serra' => array('lat' => -11.8428, 'lng' => -39.6178),
        'Pedrão' => array('lat' => -12.1375, 'lng' => -38.6250),
        'Pedro Alexandre' => array('lat' => -10.0603, 'lng' => -37.8958),
        'Piatã' => array('lat' => -13.1539, 'lng' => -41.7714),
        'Pilão Arcado' => array('lat' => -10.0039, 'lng' => -42.4881),
        'Pindaí' => array('lat' => -14.4917, 'lng' => -42.6922),
        'Pindobaçu' => array('lat' => -10.7431, 'lng' => -40.3589),
        'Pintadas' => array('lat' => -11.8136, 'lng' => -39.9014),
        'Piraí do Norte' => array('lat' => -13.7589, 'lng' => -39.3772),
        'Piripá' => array('lat' => -14.9500, 'lng' => -41.7339),
        'Piritiba' => array('lat' => -11.7303, 'lng' => -40.5589),
        'Planaltino' => array('lat' => -13.2733, 'lng' => -40.3764),
        'Planalto' => array('lat' => -14.6597, 'lng' => -40.4800),
        'Poções' => array('lat' => -14.5222, 'lng' => -40.3650),
        'Pojuca' => array('lat' => -12.4328, 'lng' => -38.3406),
        'Ponto Novo' => array('lat' => -10.8631, 'lng' => -40.1358),
        'Porto Seguro' => array('lat' => -16.4497, 'lng' => -39.0647),
        'Potiraguá' => array('lat' => -15.5950, 'lng' => -39.8836),
        'Prado' => array('lat' => -17.3406, 'lng' => -39.2214),
        'Presidente Dutra' => array('lat' => -11.2928, 'lng' => -41.9794),
        'Presidente Jânio Quadros' => array('lat' => -14.6631, 'lng' => -41.6942),
        'Presidente Tancredo Neves' => array('lat' => -13.4425, 'lng' => -39.4228),
        'Queimadas' => array('lat' => -10.9767, 'lng' => -39.6303),
        'Quijingue' => array('lat' => -10.7422, 'lng' => -39.2078),
        'Quixabeira' => array('lat' => -11.3261, 'lng' => -40.1558),
        'Rafael Jambeiro' => array('lat' => -12.3678, 'lng' => -39.5083),
        'Remanso' => array('lat' => -9.6197, 'lng' => -42.0847),
        'Retirolândia' => array('lat' => -11.4942, 'lng' => -39.4278),
        'Riachão das Neves' => array('lat' => -11.7486, 'lng' => -44.9108),
        'Riachão do Jacuípe' => array('lat' => -11.8094, 'lng' => -39.3839),
        'Riacho de Santana' => array('lat' => -13.6078, 'lng' => -42.9406),
        'Ribeira do Amparo' => array('lat' => -11.0472, 'lng' => -38.4306),
        'Ribeira do Pombal' => array('lat' => -10.8350, 'lng' => -38.5361),
        'Ribeirão do Largo' => array('lat' => -15.4497, 'lng' => -40.7331),
        'Rio de Contas' => array('lat' => -13.5794, 'lng' => -41.8103),
        'Rio do Antônio' => array('lat' => -14.4100, 'lng' => -42.0906),
        'Rio do Pires' => array('lat' => -13.1219, 'lng' => -42.3517),
        'Rio Real' => array('lat' => -11.4811, 'lng' => -37.9319),
        'Rodelas' => array('lat' => -8.8494, 'lng' => -38.7761),
        'Ruy Barbosa' => array('lat' => -12.2839, 'lng' => -40.4928),
        'Salinas da Margarida' => array('lat' => -12.8678, 'lng' => -38.7553),
        'Salvador' => array('lat' => -12.9714, 'lng' => -38.5014),
        'Santa Bárbara' => array('lat' => -11.9547, 'lng' => -38.9697),
        'Santa Brígida' => array('lat' => -9.7333, 'lng' => -38.1167),
        'Santa Cruz Cabrália' => array('lat' => -16.2778, 'lng' => -39.0250),
        'Santa Cruz da Vitória' => array('lat' => -14.9661, 'lng' => -39.8092),
        'Santa Inês' => array('lat' => -13.2778, 'lng' => -39.8178),
        'Santa Luzia' => array('lat' => -15.4300, 'lng' => -39.3311),
        'Santa Maria da Vitória' => array('lat' => -13.3861, 'lng' => -44.2006),
        'Santa Rita de Cássia' => array('lat' => -11.0078, 'lng' => -44.5208),
        'Santa Teresinha' => array('lat' => -12.7686, 'lng' => -39.5239),
        'Santaluz' => array('lat' => -11.2542, 'lng' => -39.3753),
        'Santana' => array('lat' => -13.0017, 'lng' => -44.0508),
        'Santanópolis' => array('lat' => -12.3639, 'lng' => -38.8078),
        'Santo Amaro' => array('lat' => -12.5461, 'lng' => -38.7128),
        'Santo Antônio de Jesus' => array('lat' => -12.9689, 'lng' => -39.2614),
        'Santo Estêvão' => array('lat' => -12.4319, 'lng' => -39.2522),
        'São Desidério' => array('lat' => -12.3631, 'lng' => -44.9781),
        'São Domingos' => array('lat' => -11.2814, 'lng' => -41.0369),
        'São Felipe' => array('lat' => -12.8467, 'lng' => -39.0889),
        'São Félix' => array('lat' => -12.6072, 'lng' => -38.9697),
        'São Félix do Coribe' => array('lat' => -13.4000, 'lng' => -44.1989),
        'São Francisco do Conde' => array('lat' => -12.6211, 'lng' => -38.6783),
        'São Gabriel' => array('lat' => -11.2261, 'lng' => -41.9039),
        'São Gonçalo dos Campos' => array('lat' => -12.4311, 'lng' => -38.9664),
        'São José da Vitória' => array('lat' => -15.0800, 'lng' => -39.3400),
        'São José do Jacuípe' => array('lat' => -11.4067, 'lng' => -40.0111),
        'São Miguel das Matas' => array('lat' => -13.0400, 'lng' => -39.4672),
        'São Sebastião do Passé' => array('lat' => -12.5119, 'lng' => -38.4917),
        'Sapeaçu' => array('lat' => -12.7289, 'lng' => -39.1850),
        'Sátiro Dias' => array('lat' => -11.5933, 'lng' => -38.5881),
        'Saubara' => array('lat' => -12.7389, 'lng' => -38.7661),
        'Saúde' => array('lat' => -10.9414, 'lng' => -40.4167),
        'Seabra' => array('lat' => -12.4189, 'lng' => -41.7708),
        'Sebastião Laranjeiras' => array('lat' => -10.8797, 'lng' => -42.9056),
        'Senhor do Bonfim' => array('lat' => -10.4614, 'lng' => -40.1889),
        'Sento Sé' => array('lat' => -9.7564, 'lng' => -41.8603),
        'Serra do Ramalho' => array('lat' => -13.5492, 'lng' => -43.7042),
        'Serra Dourada' => array('lat' => -12.7186, 'lng' => -42.8514),
        'Serra Preta' => array('lat' => -12.1578, 'lng' => -39.3336),
        'Serrinha' => array('lat' => -11.6639, 'lng' => -39.0086),
        'Serrolândia' => array('lat' => -11.4192, 'lng' => -40.2989),
        'Simões Filho' => array('lat' => -12.7847, 'lng' => -38.4036),
        'Sítio do Mato' => array('lat' => -13.0842, 'lng' => -43.4631),
        'Sítio do Quinto' => array('lat' => -10.3775, 'lng' => -38.2178),
        'Sobradinho' => array('lat' => -9.4575, 'lng' => -40.8219),
        'Souto Soares' => array('lat' => -12.0794, 'lng' => -41.6536),
        'Tabocas do Brejo Velho' => array('lat' => -12.6489, 'lng' => -44.0139),
        'Tanhaçu' => array('lat' => -14.0189, 'lng' => -41.2492),
        'Tanque Novo' => array('lat' => -13.5594, 'lng' => -42.4650),
        'Tanquinho' => array('lat' => -11.9722, 'lng' => -39.1258),
        'Taperoá' => array('lat' => -13.5342, 'lng' => -39.1017),
        'Tapiramutá' => array('lat' => -11.8514, 'lng' => -40.7978),
        'Teixeira de Freitas' => array('lat' => -17.5392, 'lng' => -39.7428),
        'Teodoro Sampaio' => array('lat' => -12.3164, 'lng' => -38.6092),
        'Teofilândia' => array('lat' => -11.4203, 'lng' => -39.0522),
        'Teolândia' => array('lat' => -13.5308, 'lng' => -39.4978),
        'Terra Nova' => array('lat' => -12.3347, 'lng' => -38.7211),
        'Tremedal' => array('lat' => -14.9697, 'lng' => -41.4128),
        'Tucano' => array('lat' => -10.9631, 'lng' => -38.7878),
        'Uauá' => array('lat' => -9.8328, 'lng' => -39.4836),
        'Ubaíra' => array('lat' => -13.2744, 'lng' => -39.6606),
        'Ubaitaba' => array('lat' => -14.3056, 'lng' => -39.3239),
        'Ubatã' => array('lat' => -14.2078, 'lng' => -39.5233),
        'Uibaí' => array('lat' => -11.3397, 'lng' => -42.1361),
        'Umburanas' => array('lat' => -10.7333, 'lng' => -41.3236),
        'Una' => array('lat' => -15.2839, 'lng' => -39.0753),
        'Urandi' => array('lat' => -14.7639, 'lng' => -42.6522),
        'Uruçuca' => array('lat' => -14.5928, 'lng' => -39.2850),
        'Utinga' => array('lat' => -12.0800, 'lng' => -41.0961),
        'Valença' => array('lat' => -13.3708, 'lng' => -39.0728),
        'Valente' => array('lat' => -11.4094, 'lng' => -39.4631),
        'Várzea da Roça' => array('lat' => -11.6103, 'lng' => -40.1208),
        'Várzea do Poço' => array('lat' => -11.5206, 'lng' => -40.3403),
        'Várzea Nova' => array('lat' => -11.2617, 'lng' => -40.9447),
        'Varzedo' => array('lat' => -12.9703, 'lng' => -39.3939),
        'Vera Cruz' => array('lat' => -13.0097, 'lng' => -38.6219),
        'Vereda' => array('lat' => -10.9808, 'lng' => -40.9247),
        'Vitória da Conquista' => array('lat' => -14.8661, 'lng' => -40.8394),
        'Wagner' => array('lat' => -12.2856, 'lng' => -41.1678),
        'Wanderley' => array('lat' => -12.1222, 'lng' => -43.8867),
        'Wenceslau Guimarães' => array('lat' => -13.5950, 'lng' => -39.4769),
        'Xique-Xique' => array('lat' => -10.8231, 'lng' => -42.7269)
    );
}

/**
 * 1) Registrar post type 'associate'
 */
function associates_register_post_type() {
    register_post_type('associate', array(
        'labels' => array(
            'name' => 'Associados',
            'singular_name' => 'Associado',
            'add_new' => 'Adicionar Novo',
            'add_new_item' => 'Adicionar Novo Associado',
            'edit_item' => 'Editar Associado',
            'new_item' => 'Novo Associado',
            'view_item' => 'Ver Associado',
            'search_items' => 'Buscar Associados',
            'not_found' => 'Nenhum associado encontrado',
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'thumbnail'),
        'show_in_rest' => true,
        'rewrite' => false,
        'publicly_queryable' => false,
    ));
}
add_action('init', 'associates_register_post_type', 0);

/**
 * Alterar placeholder do título para associados
 */
function associates_change_title_placeholder($title) {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'associate') {
        $title = 'Adicionar título do Associado';
    }
    return $title;
}
add_filter('enter_title_here', 'associates_change_title_placeholder');

/**
 * Alterar texto do Featured Image para associados
 */
function associates_change_featured_image_text($content, $post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'associate') {
        $content = str_replace('Featured image', 'Foto do Associado', $content);
        $content = str_replace('Set featured image', 'Definir foto do associado', $content);
        $content = str_replace('Remove featured image', 'Remover foto do associado', $content);
        $content = str_replace('Replace featured image', 'Substituir foto do associado', $content);
    }
    return $content;
}
add_filter('admin_post_thumbnail_html', 'associates_change_featured_image_text', 10, 2);
/**
 * 2) Registrar taxonomy 'associado_categoria' e criar termos padrão (se não existirem)
 */
function associates_register_taxonomy_and_terms() {
    register_taxonomy('associate_category', 'associate', array(
        'labels' => array(
            'name' => 'Categorias de Associado',
            'singular_name' => 'Categoria de Associado',
        ),
        'hierarchical' => false,
        'show_ui' => true,  
        'show_in_rest' => true,
    ));

    $terms = array(
        'Amante de queijo','Chef de cozinha','Consultor','Cooperativa','Curador',
        'Leite Cru','Leite de Búfala','Leite de Cabra','Leite de Ovelha','Leite de Vaca',
        'Leite Pasteurizado','Pesquisador','Produtor','Queijista','Técnico em Laticínios',
        'Todos os Associados','Todos os Tipos de Ator','Todos os Tipos de Leite',
        'Todos os Tipos de Queijo','Todos os Tratamentos Térmicos'
    );

    foreach ($terms as $t) {
        if (!term_exists($t, 'associate_category')) {
            wp_insert_term($t, 'associate_category');
        }
    }
}
add_action('init', 'associates_register_taxonomy_and_terms', 5);

/**
 * 3) Metabox para infos: descrição e município
 */
function associates_add_metabox() {
    add_meta_box('associates_info', 'Informações do Associado', 'associates_metabox_callback', 'associate', 'normal', 'default');
}
add_action('add_meta_boxes', 'associates_add_metabox');

function associates_metabox_callback($post) {
    $description = get_post_meta($post->ID, '_wpa_description', true);
    $municipality = get_post_meta($post->ID, '_wpa_municipality', true);

    wp_nonce_field('associates_save_meta', 'associates_nonce');

    echo '<p><label><strong>Descrição</strong></label><br/><textarea name="associates_description" rows="4" style="width:100%; resize:vertical;">'.esc_textarea($description).'</textarea></p>';

    echo '<p><label><strong>Município</strong></label><br/>';
    echo '<select name="associates_municipality" style="width:100%; padding-horizontal:4px; height:auto;">';
    echo '<option value="">Selecione um município</option>';
    
    $municipalities = associates_get_bahia_municipalities();
    foreach ($municipalities as $name => $coords) {
        $selected = ($municipality === $name) ? 'selected' : '';
        echo '<option value="'.esc_attr($name).'" '.$selected.'>'.esc_html($name).'</option>';
    }
    
    echo '</select>';
    echo '<small>Selecione o município onde o associado está localizado</small>';
    echo '</p>';
}


function associates_save_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['associates_nonce']) || !wp_verify_nonce($_POST['associates_nonce'], 'associates_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Salvar descrição
    if (isset($_POST['associates_description'])) {
        update_post_meta($post_id, '_wpa_description', sanitize_textarea_field($_POST['associates_description']));
    }
    
    // Salvar município e suas coordenadas
    if (isset($_POST['associates_municipality'])) {
        $municipality = sanitize_text_field($_POST['associates_municipality']);
        update_post_meta($post_id, '_wpa_municipality', $municipality);
        
        // Buscar coordenadas do município selecionado
        if (!empty($municipality)) {
            $municipalities = associates_get_bahia_municipalities();
            if (isset($municipalities[$municipality])) {
                update_post_meta($post_id, '_wpa_latitude', $municipalities[$municipality]['lat']);
                update_post_meta($post_id, '_wpa_longitude', $municipalities[$municipality]['lng']);
            }
        }
    }
}
add_action('save_post', 'associates_save_meta');
/**
 * 4) Enfileirar scripts e estilos (Leaflet + CSS do plugin)
 */
function associates_enqueue_scripts() {
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);

    // Usar filemtime para versionar e evitar cache
    $css_version = filemtime(plugin_dir_path(__FILE__) . 'styles.css');
    wp_register_style('associates-css', plugin_dir_url(__FILE__) . 'styles.css', array(), $css_version);
    wp_enqueue_style('associates-css');
}
add_action('wp_enqueue_scripts', 'associates_enqueue_scripts');


/**
 * 
 * 6) Shortcode [associados_interativo]
 */
function associates_shortcode($atts) {
    ob_start();

    // Query: todos os associados
    $query = new WP_Query(array('post_type' => 'associate','posts_per_page' => -1));

    // Buscamos as categorias disponíveis (taxonomy)
    $terms = get_terms(array('taxonomy' => 'associate_category', 'hide_empty' => false));

    ?>
    <div class="associates-wrapper">
        <div class="associates-filters">
            <input type="text" id="associates-search-associate" placeholder="Buscar por nome ou descrição">

            <select id="associates-municipality-filter">
                <option value="">Todos os Municípios</option>
                <?php
                    $municipalities = associates_get_bahia_municipalities();
                    foreach ($municipalities as $mun_name => $coords) {
                        echo '<option value="'.esc_attr($mun_name).'">'.esc_html($mun_name).'</option>';
                    }
                ?>
            </select>

            <select id="associates-category-associate">
                <option value="">Todas as Categorias</option>
                <?php
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $t) {
                            echo '<option value="'.esc_attr($t->term_id).'">'.esc_html($t->name).'</option>';
                        }
                    }
                ?>
            </select>

            <button id="associates-filter-associates" style="width: 200px;">Filtrar Associados</button>
        </div>

        <div class="associates-parent-div">
            <div id="associates-container" class="associates-list">
                <?php while ($query->have_posts()) : $query->the_post();
                    $municipality = get_post_meta(get_the_ID(), '_wpa_municipality', true);
                    $lat = get_post_meta(get_the_ID(), '_wpa_latitude', true);
                    $lng = get_post_meta(get_the_ID(), '_wpa_longitude', true);
                    $description = get_post_meta(get_the_ID(), '_wpa_description', true);
                    $image = get_the_post_thumbnail(get_the_ID(), 'medium');
                    $terms_assoc = wp_get_post_terms(get_the_ID(), 'associate_category', array('fields'=>'ids'));
                    $terms_assoc_json = esc_attr(json_encode($terms_assoc));
                ?>
                <div class="associate" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>"
                     data-name="<?php echo esc_attr(get_the_title()); ?>" data-description="<?php echo esc_attr($description); ?>"
                     data-municipality="<?php echo esc_attr($municipality); ?>" data-cats='<?php echo $terms_assoc_json; ?>'>
                    <div class="associate-thumb">
                        <?php
                            if ($image) echo $image;
                            else echo '<img src="'.esc_url(plugins_url('placeholder.png', __FILE__)).'" alt="sem imagem" />';
                        ?>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <div class="associates-description-container">
                        <?php 
                        $description_length = strlen($description);
                        if ($description_length > 150) {
                            $short_desc = substr($description, 0, 150) . '...';
                            echo '<p class="associates-description-short">' . esc_html($short_desc) . '</p>';
                            echo '<p class="associates-description-full" style="display:none;">' . esc_html($description) . '</p>';
                            echo '<button class="associates-read-more" onclick="toggleDescription(this)">Leia mais...</button>';
                        } else {
                            echo '<p class="associates-description">' . esc_html($description) . '</p>';
                        }
                        ?>
                    </div>
                    <p class="associates-local"><?php echo esc_html($municipality); ?> - BA</p>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div id="map" ></div>
        </div>
    </div>

    <!-- Modal para exibir informações do associado -->
    <div id="associates-modal" class="associates-modal" style="display: none;">
        <div class="associates-modal-content">
            <span class="associates-modal-close">&times;</span>
            <div id="associates-modal-body"></div>
        </div>
    </div>

    <script>
    // Função para alternar descrição
    function toggleDescription(button) {
        var container = button.parentNode;
        var shortDesc = container.querySelector('.associates-description-short');
        var fullDesc = container.querySelector('.associates-description-full');
        
        if (fullDesc.style.display === 'none') {
            shortDesc.style.display = 'none';
            fullDesc.style.display = 'block';
            button.textContent = 'Leia menos...';
        } else {
            shortDesc.style.display = 'block';
            fullDesc.style.display = 'none';
            button.textContent = 'Leia mais...';
        }
    }

    // Função para mostrar modal do associado
    function showAssociateModal(name, description, municipality, imgOuter) {
        var modal = document.getElementById('associates-modal');
        var modalBody = document.getElementById('associates-modal-body');
        
        var modalContent = '<div class="associates-modal-header">' +
            '<div class="associates-modal-image">' + imgOuter + '</div>' +
            '<div class="associates-modal-info">' +
                '<h3>' + name + '</h3>' +
                '<p class="associates-modal-location">' + municipality + ' - BA</p>' +
            '</div>' +
        '</div>' +
        '<div class="associates-modal-description">' + description + '</div>';
        
        modalBody.innerHTML = modalContent;
        modal.classList.add('show');
    }

    // Função para fechar modal
    function closeAssociateModal() {
        var modal = document.getElementById('associates-modal');
        modal.classList.remove('show');
    }
    
    (function(){
        // roda quando DOM pronto
        document.addEventListener('DOMContentLoaded', function(){
            // init map - centraliza na Bahia
            var map = L.map('map').setView([-12.5797, -41.7007], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a>'
            }).addTo(map);

            var markers = [];
            var markerGroup = L.layerGroup().addTo(map);

            function createDivIconFromImage(imgHtml){
                var html = '<div class="associates-marker-icon">'+ imgHtml +'</div>';
                return L.divIcon({
                    html: html,
                    className: 'ai-marker-wrapper',
                    iconSize: [56,56],
                    iconAnchor: [28,56],
                    popupAnchor: [0,-56]
                });
            }

            document.querySelectorAll('.associate').forEach(function(el){
                var lat = parseFloat(el.dataset.lat);
                var lng = parseFloat(el.dataset.lng);
                var name = el.dataset.name || '';
                var description = el.dataset.description || '';
                var municipality = el.dataset.municipality || '';
                var cats = [];
                try { cats = JSON.parse(el.dataset.cats); } catch(e){ cats = []; }

                var img = el.querySelector('img');
                var imgOuter = img ? img.outerHTML : '<div class="associates-noimg">?</div>';

                if (!isNaN(lat) && !isNaN(lng)) {
                    var icon = createDivIconFromImage(imgOuter);

                    var marker = L.marker([lat,lng], {icon: icon});
                    
                    // Ao invés de popup, usar click para abrir modal
                    marker.on('click', function() {
                        showAssociateModal(name, description, municipality, imgOuter);
                    });
                    
                    marker.addTo(markerGroup);

                    markers.push({el: el, marker: marker, lat: lat, lng: lng, cats: cats, municipality: municipality, name: name, description: description});
                } else {
                    markers.push({el: el, marker: null, lat: null, lng: null, cats: cats, municipality: municipality, name: name, description: description});
                }
            });

            // funções utilitárias
            function deg2rad(deg){ return deg * (Math.PI/180); }
            function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2){
                var R=6371;
                var dLat=deg2rad(lat2-lat1);
                var dLon=deg2rad(lon2-lon1);
                var a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
                var c=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
                return R*c;
            }

            // filtrar - aplica filtros visuais e no mapa
            function applyFilters(){
                var search = (document.getElementById('associates-search-associate').value || '').toLowerCase();
                var municipality = document.getElementById('associates-municipality-filter').value; // município selecionado
                var category = document.getElementById('associates-category-associate').value; // term_id ou ''

                // limpar group
                markerGroup.clearLayers();

                markers.forEach(function(m){
                    var show = true;

                    // busca por nome + descrição
                    var hay = (m.name + ' ' + (m.description||'')).toLowerCase();
                    if (search && hay.indexOf(search) === -1) show = false;

                    // município
                    if (municipality && m.municipality !== municipality) show = false;

                    // categoria (m.cats é array de term_ids)
                    if (category) {
                        var catNum = parseInt(category,10);
                        if (!m.cats || m.cats.indexOf(catNum) === -1) show = false;
                    }

                    // aplicar visibilidade na lista
                    if (show) {
                        m.el.style.display = 'block';
                        if (m.marker) markerGroup.addLayer(m.marker);
                    } else {
                        m.el.style.display = 'none';
                        // marker não adicionado ao group => sumirá do mapa
                    }
                });

                // Se houver marcadores no mapa, ajusta bounds
                var allMarkers = [];
                markerGroup.eachLayer(function(l){ allMarkers.push(l.getLatLng()); });
                if (allMarkers.length > 0) {
                    var bounds = L.latLngBounds(allMarkers);
                    map.fitBounds(bounds.pad(0.25));
                } else {
                    // se nenhum marker visível, centraliza na Bahia
                    map.setView([-12.5797, -41.7007], 6);
                }
            }

            // eventos: em tempo real (change) e botão
            document.getElementById('associates-search-associate').addEventListener('input', applyFilters);
            document.getElementById('associates-municipality-filter').addEventListener('change', applyFilters);
            document.getElementById('associates-category-associate').addEventListener('change', applyFilters);
            document.getElementById('associates-filter-associates').addEventListener('click', applyFilters);

            // aplicar inicialmente (mostra todos)
            applyFilters();

            // Event listeners para fechar modal
            document.querySelector('.associates-modal-close').addEventListener('click', closeAssociateModal);
            document.getElementById('associates-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAssociateModal();
                }
            });

            // clicar no card centraliza e abre modal
            document.querySelectorAll('.associate').forEach(function(card){
                card.addEventListener('click', function(){
                    // encontrar marker associado
                    var name = card.dataset.name;
                    var description = card.dataset.description;
                    var municipality = card.dataset.municipality;
                    var img = card.querySelector('img');
                    var imgOuter = img ? img.outerHTML : '<div class="associates-noimg">?</div>';
                    
                    var found = markers.find(function(m){ return m.name === name && m.marker; });
                    if (found && found.marker) {
                        map.setView(found.marker.getLatLng(), 12);
                        showAssociateModal(name, description, municipality, imgOuter);
                    }
                });
            });

        });
    })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('wp-associates', 'associates_shortcode');
