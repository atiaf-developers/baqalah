<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationType extends MyModel
{
    protected $table = "donation_types";

	public function translations() {
		return $this->hasMany(DonationTypeTranslation::class, 'donation_type_id');
	}

	public static function transform($item)
	{
		$transformer = new \stdClass();
		$transformer->id = $item->id;
		$transformer->title = $item->title;

        return $transformer;
		
	}



	protected static function boot() {
		parent::boot();

		static::deleting(function($donation_type) {
			foreach ($donation_type->translations as $translation) {
				$translation->delete();
			}
		});

		
	}


}
