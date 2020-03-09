<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'wp-directory' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'paracetamol' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'hPEC[k.OM]cOBE;eu}5/C-d#i46lt8`ee@oM^N16]Yx;cP86%p;B6JVT[RNpOUAv' );
define( 'SECURE_AUTH_KEY',  'ILHTvm5LNgl}y)V7%FN0whWN,o%{2x/MU)> Ux^-%0d(pD3v,:7VP<w|6?F68^{g' );
define( 'LOGGED_IN_KEY',    ']EhV$q&nO3G;*%SWUzR dO,:|Y[Sm5)`]&IB44M^99)i,sl/{qlwY4I1DSf=:&Ju' );
define( 'NONCE_KEY',        'MB^)vIfs|^$o:]*eC3}pBtqC.J#lVOwh[NP:UnEC4&,1p8}0#-pz@Y;s`!ozJMY<' );
define( 'AUTH_SALT',        '1Qv pS??>Uq`LH,>_4ADG=wg7AOne,%P>q:)v<-;]N$Z:HOh|W/g+n/0Lzuz>`Iv' );
define( 'SECURE_AUTH_SALT', 'tq)Wn>zlh&?jJ]XD3JX(j3::Nz^C{CQ4XkarGT,AXe3?,!`,W b1<GI>l`!0&9zu' );
define( 'LOGGED_IN_SALT',   'RBC)e[:#I0f^`B$ L(sR_@ZYL=hF*dLEnm3U6p.o#YtLyunOv2Hth5`Xh^79bWK3' );
define( 'NONCE_SALT',       ';lxqs_A=S?G3B@-JWUCyIQh&L3U^?ZOztj5CWK,6tCLpz=8?gg]U)0nL5mtEB9oS' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
