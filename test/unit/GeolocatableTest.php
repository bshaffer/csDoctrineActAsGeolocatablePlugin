<?php

require_once dirname(__FILE__).'/../bootstrap/bootstrap.php';

$t = new lime_test(12);

$t->info('Test Geolocatable Plugin locates by City State and Zip');

  $article = new GeolocatableArticle_CityStateZip();
  $article->name  = 'Testing this out';
  $article->city  = 'Nashville';
  $article->state = 'TN';
  $article->zip   = 37211;
  $article->save();
        
  $t->info("  Verify at http://maps.google.com/maps?hl=en&q={$article->latitude},+{$article->longitude}");
  $t->is($article->latitude, 36.0558177);
  $t->is($article->longitude, -86.7315785);

$t->info('Test Geolocatable Plugin locates with missing fields');

  $article = new GeolocatableArticle_CityStateZip();
  $article->name  = 'Testing this out';
  $article->zip   = 37211;
  $article->save();
  
  $t->info("  Verify at http://maps.google.com/maps?hl=en&q={$article->latitude},+{$article->longitude}");
  $t->is($article->latitude, 36.0558177);
  $t->is($article->longitude, -86.7315785);

$t->info('Test Geolocatable Plugin locates by address');

  $article = new GeolocatableArticle_Address();
  $article->name      = "Eiffel Tower";
  $article->address   = 'Tour Eiffel Champ de Mars 75007 Paris, France';
  $article->save();
  
  $t->info("  Verify at http://maps.google.com/maps?hl=en&q={$article->latitude},+{$article->longitude}");
  $t->is($article->latitude, 48.8582635);
  $t->is($article->longitude, 2.2942543);

$t->info('Test Geolocatable Plugin updates latitude / longitude automatically when saved');

  $article = new GeolocatableArticle_CityStateZip();
  $article->name  = 'Testing this out';
  $article->city  = 'Nashville';
  $article->state = 'TN';
  $article->zip   = 37211;
  $article->save();
  

  $t->info("  Verify at http://maps.google.com/maps?hl=en&q={$article->latitude},+{$article->longitude}");
  $t->is($article->latitude, 36.0558177);
  $t->is($article->longitude, -86.7315785);

  // Latitude / Longitude data changes with new zipcode
  $article->zip = 37212;
  $article->save();
  
  $t->info("  Verify at http://maps.google.com/maps?hl=en&q={$article->latitude},+{$article->longitude}");
  $t->is($article->latitude, 36.1281626);
  $t->is($article->longitude, -86.7969244);
  
  // Latitude / Longitude does not change if data is not updated
  $article->latitude = 0;
  $article->longitude = 0;
  $article->save();      
  
  $t->is($article->latitude, 0);
  $t->is($article->longitude, 0);
