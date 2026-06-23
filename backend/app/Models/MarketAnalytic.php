<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class MarketAnalytic extends Model { use HasUuids; protected $table = 'market_analytics'; protected $fillable = ['type','segment','country','city','avg_price','min_price','max_price','median_price','listing_count','demand_index','supply_index','price_trend','breakdown','period_date']; protected function casts(): array { return ['breakdown' => 'array', 'period_date' => 'date']; } }
