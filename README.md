# Basketball Predictions (The Primeagen)

### Install
```bash
$ composer install
$ npm install
$ sail up -d 
$ sail artisan test
```

### Tests Results
```bash

   PASS  Tests\Feature\IrcMessageTest
  ✓ parsing full message                                                                                                                                                                                                     0.01s  
  ✓ parsing join message
  ✓ parsing part message
  ✓ parsing notice message
  ✓ to string

   PASS  Tests\Feature\PredictionMessageTest
  ✓ push message returns with data set #0                                                                                                                                                                                    0.18s  
  ✓ push message returns with data set #1                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #2                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #3                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #4                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #5                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #6                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #7                                                                                                                                                                                    0.01s  
  ✓ push message returns with data set #8                                                                                                                                                                                    0.01s  
  ✓ push message returns output                                                                                                                                                                                              0.01s  

   PASS  Tests\Feature\PredictionScoreTest
  ✓ prediction points new prediction                                                                                                                                                                                         0.02s  
  ✓ prediction points full calculation                                                                                                                                                                                       0.01s  

  Tests:    17 passed (41 assertions)
  Duration: 0.36s

```

### Routes

- http://localhost/adam
