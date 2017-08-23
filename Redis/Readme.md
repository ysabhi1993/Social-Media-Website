### Installing Redis
- Follow below commands
   ```
   $ curl -O http://download.redis.io/redis-stable.tar.gz

   $ tar xzvf redis-stable.tar.gz

   $ cd redis-stable

   $ make && make test

   $ sudo make install
   ```
### Configure Redis
- Follow below commands
    ```
    $ sudo mkdir /etc/redis #create a new directory

    $ sudo cp /home/abhishek/redis-stable/redis.conf /etc/redis #copy the redis.config file into  the new directory

    $ sudo gedit /etc/redis/redis.conf # or with any other text editor
    ```
- Make these changes in the redis.conf file
    ```
    supervised no to supervised systemd
    
    dir to dir /var/lib/redis # for persistent data dump
    ```
- Setting up Service file

  Create a file with .service extension. The name of the file will be used to start/stop Redis as a service.
    ```
    sudo gedit /etc/systemd/system/redis.servic
    ```
  Add the below content to the file. Make sure the config filename is the one you created in the above step.
    ```
    [Unit]
    Description=Redis In-Memory Data Store
    After=network.target

    [Service]
    User=redis
    Group=redis
    ExecStart=/usr/local/bin/redis-server /etc/redis/redis.conf
    ExecStop=/usr/local/bin/redis-cli shutdown
    Restart=always

    [Install]
    WantedBy=multi-user.target
    ```

### predis - Redis for PHP
  - Get the predis files from this repo - [predis](https://github.com/nrk/predis.git) 
  - Make sure to use correct directory names when including files.

### Creating multiple instances
  #### Changes to .conf file
  - Create copies of .conf file and name them consistently(for example with the instance names). 
  - Change the IP address to which the cache should listen to.
  - Change the port number on which the cache.
  ### Changes to .service file
  - Create copies of this file and name them consistently like the .config file.
  - Change the ExecutionStart variable to direct to .conf file. 
  
### Starting the service
```
$ sudo service (name of the .service file without extension) start
```
### Stopping the service
```
$ sudo service (name of the .service file without extension) stop
```
### Check Status
```
$ sudo service (name of the .service file without extension) status
```
#### Output looks like this
```
redis_6379.service - Redis In-Memory Data Store
   Loaded: loaded (/etc/systemd/system/redis_6379.service; disabled; vendor preset: enabled)
   Active: active (running) since Wed 2017-08-23 15:49:11 EDT; 30min ago
 Main PID: 5839 (redis-server)
    Tasks: 4 (limit: 4915)
   CGroup: /system.slice/redis_6379.service
           └─5839 /usr/local/bin/redis-server 127.0.0.1:6379
```
  
