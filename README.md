# nasa_task

###1. Command to import/refresh polish holidays :

##php bin/console app:refresh:holidays

###1. Command to import/refresh images from nasa api (Mars Rover Photos):

##php bin/console app:refresh:images

###1. endpoint a) :
```
http://your_app_address/api/get_photos?date={date}&rover={rover}&camera={camera}
```
param| options
------------ | -------------
camera | FHAZ, RHAZ
rover | curiosity, opportunity, spirit
date | format: YYYY-MM-DD

###1. endpoint b) :
```
http://your_app_address/api/get_photos_details/{photo_id}
```
param| options
------------ | -------------
photo_id | id of photo from (a endpoint

    