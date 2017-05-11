import RPi.GPIO as GPIO
import time
import json
import requests

token = 'roelgarden'
url = 'http://www.xx.xx/status.php?token='
pump = 4
sensor = 5

pauseTime = 10

mode = 'off'
GPIO.cleanup()
GPIO.setmode(GPIO.BCM)
GPIO.setup(pump,GPIO.OUT,initial=GPIO.HIGH)


while True:
    statusJson = requests.get(url + token).json();
    status = statusJson['status']
    if status == 'water':
        waterTime = float(statusJson['seconds'])
        GPIO.output(pump,GPIO.LOW)
        print('Water requested: starting pump')
        time.sleep(waterTime)
        GPIO.output(pump,GPIO.HIGH)
        statusJson = requests.get("http://www.roelpeters.be/egarden/status.php?token=roelgarden").json();
    if status == 'notoken':
        print('No valid token.')
    else:
        print('No water requested')
        time.sleep(pauseTime)

GPIO.cleanup()