<?php

namespace App\Http\Controllers;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'section' => 'Compte & sécurité',
                'items' => [
                    ['q' => 'Comment créer un compte ?', 'a' => 'Cliquez sur "S\'inscrire", renseignez vos informations, puis validez. Un e-mail de vérification vous sera envoyé.'],
                    ['q' => 'Je n’ai pas reçu l’e-mail de vérification.', 'a' => 'Vérifiez vos spams puis cliquez sur "Renvoyer l’e-mail" depuis la page de vérification.'],
                    ['q' => 'Comment réinitialiser mon mot de passe ?', 'a' => 'Depuis la page de connexion, utilisez "Mot de passe oublié" pour recevoir un lien de réinitialisation.'],
                    ['q' => 'Mes données sont-elles sécurisées ?', 'a' => 'LOBIKO chiffre les données sensibles, impose une vérification e-mail et limite l’accès aux dossiers de santé selon les rôles.'],
                ],
            ],
            [
                'section' => 'Rendez-vous & téléconsultations',
                'items' => [
                    ['q' => 'Comment prendre rendez-vous ?', 'a' => 'Depuis "Mes rendez-vous", choisissez un praticien/structure, une date et confirmez.'],
                    ['q' => 'Puis-je annuler ou reprogrammer ?', 'a' => 'Oui, selon les conditions du praticien. Vous pouvez annuler ou proposer un autre créneau depuis votre espace.'],
                    ['q' => 'Accès téléconsultation ?', 'a' => 'Une fois confirmée, un lien sécurisé est disponible dans votre rendez-vous ; seul le patient et le praticien autorisés peuvent rejoindre.'],
                    ['q' => 'Problèmes techniques (son/vidéo) ?', 'a' => 'Vérifiez vos autorisations micro/caméra, changez de réseau si possible, puis relancez la session.'],
                ],
            ],
            [
                'section' => 'e-Pharmacie & ordonnances',
                'items' => [
                    ['q' => 'Comment joindre une ordonnance ?', 'a' => 'Lors de la commande, chargez votre ordonnance (PDF/JPEG). Elle est stockée en privé et accessible uniquement aux pharmaciens habilités.'],
                    ['q' => 'La pharmacie peut-elle refuser ?', 'a' => 'Oui, si l’ordonnance est invalide ou si le médicament est indisponible. Une substitution autorisée peut être proposée.'],
                    ['q' => 'Comment est établi le devis ?', 'a' => 'Le pharmacien analyse l’ordonnance, vérifie le stock et propose un devis détaillé (prix, substitutions, commission plateforme si applicable).'],
                    ['q' => 'Qui peut voir l’ordonnance ?', 'a' => 'Seuls le patient, le pharmacien/dispensateur et l’administrateur autorisé via policy.'],
                ],
            ],
            [
                'section' => 'Paiements (Carte & Mobile Money)',
                'items' => [
                    ['q' => 'Quels moyens de paiement ?', 'a' => 'Carte bancaire (Visa/MasterCard) et Mobile Money (MTN MoMo, Airtel Money, Orange Money).'],
                    ['q' => 'Paiement échoué, que faire ?', 'a' => 'Réessayez avec une connexion stable. Si le débit est visible sans validation, le statut est vérifié côté PSP/Opérateur avant confirmation.'],
                    ['q' => 'Commission LOBIKO ?', 'a' => 'La commission de la plateforme est intégrée dans le devis/facture de façon transparente.'],
                    ['q' => 'Remboursements ?', 'a' => 'En cas d’annulation ou d’échec, un remboursement peut être initié selon le statut de la commande/paiement.'],
                ],
            ],
            [
                'section' => 'Livraisons',
                'items' => [
                    ['q' => 'Comment suivre la livraison ?', 'a' => 'Depuis le suivi de commande : statut, livreur assigné, ETA, preuves (photo/OTP/signature).'],
                    ['q' => 'Zones et délais ?', 'a' => 'Les pharmacies définissent leurs zones et délais ; le devis inclut les frais et estimations.'],
                    ['q' => 'Preuve de livraison ?', 'a' => 'OTP ou photo/signature est demandée pour marquer la livraison comme effectuée.'],
                ],
            ],
            [
                'section' => 'Assurance & prise en charge',
                'items' => [
                    ['q' => 'Conventions assureur-prestataire ?', 'a' => 'LOBIKO gère des conventions avec taux de couverture, plafonds et délais de remboursement paramétrables.'],
                    ['q' => 'Reste à charge ?', 'a' => 'Le calcul applique la règle de couverture (taux, plafonds, franchise/ticket modérateur) et affiche le reste à charge.'],
                    ['q' => 'Remboursement ?', 'a' => 'Pour le POST-PAY, une demande de remboursement (claim) est générée avec une échéance selon la convention.'],
                ],
            ],
            [
                'section' => 'Support',
                'items' => [
                    ['q' => 'Comment contacter le support ?', 'a' => 'Via la page Contact ou l’email support ; joignez votre numéro de commande ou rendez-vous.'],
                    ['q' => 'Horaires ?', 'a' => 'Support standard : 8h-20h GMT+1 (élargi en cas d’urgence critique).'],
                    ['q' => 'Signaler un incident ?', 'a' => 'Utilisez le formulaire support ou contactez un administrateur via l’espace sécurisé.'],
                ],
            ],
        ];

        return view('faq', compact('faqs'));
    }
}
