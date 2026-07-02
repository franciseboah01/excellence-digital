<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NiveauFormation extends Model
{
    protected $table = 'niveaux_formation';

    protected $fillable = [
        'formation_id', 'nom', 'ordre', 'description'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function ressources()
    {
        return $this->hasMany(Ressource::class, 'niveau_id');
    }

    public function qcms()
    {
        return $this->hasMany(Qcm::class, 'niveau_id');
    }

    /**
     * ✅ Ce niveau est-il validé par un utilisateur donné ?
     * Un niveau est validé si tous ses QCMs actifs ont été réussis
     * au moins une fois. Un niveau sans QCM actif est considéré
     * automatiquement validé (rien à valider dessus).
     *
     * Centralisé ici pour être utilisé à la fois par le contrôleur QCM
     * (bloquer l'accès au QCM du niveau suivant) et par le contrôleur
     * client (verrouiller l'accès aux ressources du niveau suivant) —
     * une seule source de vérité pour éviter toute incohérence.
     */
    public function estValidePar(int $userId): bool
    {
        $qcmsActifs = $this->qcms()->where('actif', true)->get();

        if ($qcmsActifs->isEmpty()) {
            return true;
        }

        foreach ($qcmsActifs as $qcm) {
            $reussi = SessionQcm::where('qcm_id', $qcm->id)
                ->where('user_id', $userId)
                ->where('reussi', true)
                ->exists();

            if (!$reussi) {
                return false;
            }
        }

        return true;
    }

    /**
     * ✅ Ce niveau est-il accessible pour un utilisateur donné ?
     * Accessible si c'est le premier niveau de la formation, ou si le
     * niveau précédent (ordre inférieur) est validé.
     */
    public function estAccessiblePar(int $userId): bool
    {
        $niveauPrecedent = self::where('formation_id', $this->formation_id)
            ->where('ordre', '<', $this->ordre)
            ->orderByDesc('ordre')
            ->first();

        if (!$niveauPrecedent) {
            return true;
        }

        return $niveauPrecedent->estValidePar($userId);
    }
}