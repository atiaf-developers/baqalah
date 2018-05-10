<?php

namespace App\Traits;

use App\Models\Rating;
use App\Models\RatingUser;

trait Rate {

    protected function new_rate($entity_id, $score, $user_id, $comment, $type = 1) {
        //dd($score);
        if ($rating = $this->exist($entity_id, $score, $type)) {
            $this->updateRate($rating, $user_id, $comment);
        } else {
            $this->addRate($entity_id, $score, $user_id, $comment, $type);
        }
    }

    public function countRates($entity_id, $type = 1) {
        $Rating = Rating::where('entity_id', $entity_id)->where('type', $type)->orderBy('score', 'ASC')->get();
        $x = 0;
        $y = 0;
        if ($Rating->count() > 0) {
            foreach ($Rating as $one) {
                $x += $one->total_rates * $one->score;
                $y += $one->total_rates;
            }
        }
        return ceil($x / $y);
        //return floor($x / $y);
    }

    private function addRate($entity_id, $score, $user_id, $comment,$type) {
        $Rating = new Rating;
        $Rating->entity_id = $entity_id;
        $Rating->type = $type;
        $Rating->score = $score;
        $Rating->total_rates = 1;
        $Rating->save();

        $RatingUser = new RatingUser;
        $RatingUser->rating_id = $Rating->id;
        $RatingUser->user_id = $user_id;
        $RatingUser->comment = $comment;
        $RatingUser->save();
        return $Rating;
    }

    private function updateRate($rating, $user_id, $comment) {
        $rating->total_rates = $rating->total_rates + 1;
        $rating->save();

        $RatingUser = new RatingUser;
        $RatingUser->rating_id = $rating->id;
        $RatingUser->user_id = $user_id;
        $RatingUser->comment = $comment;
        $RatingUser->save();
        return $rating;
    }

    private function exist($entity_id, $score,$type) {
        $Rating = Rating::where('entity_id', $entity_id)->where('type', $type)->where('score', $score)->first();
        return $Rating;
    }

}
