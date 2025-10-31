# /artists

`http://localhost:8888/php-music/artists`

---


### `GET` : get all artists
params: `offset`, `limit`

---

### `GET` : get artist by ID
params: `id`

---

### `GET` : get all releases by artist (discography)
params: `discography`

---

### `POST` : create new artist
body: `name`

headers: `X-Authorization` = `1234`

---

### `PUT` : edit artist
params: `id`

body: `name`

headers: `X-Authorization` = `1234`

---

### `DELETE` : delete artist
params: `id`

headers: `X-Authorization` = `1234`


<br><br>



# /releases

`http://localhost:8888/php-music/releases`

---


### `GET` : get all releases
params: `offset`, `limit`

---

### `GET` : get release by ID
params: `id`

---

### `GET` : get all songs by release (tracklist)
params: `tracklist`

---

### `POST` : create new release
body: `title`, `release_year`, `release_type`, `artwork_url`

headers: `X-Authorization` = `1234`

---

### `PUT` : edit release
params: `id`

body: `id`, `title`, `release_year`, `release_type`, `artwork_url`

headers: `X-Authorization` = `1234`

---

### `DELETE` : delete release
params: `id`

headers: `X-Authorization` = `1234`


<br><br>



# /songs

`http://localhost:8888/php-music/songs`

---


### `GET` : get all songs
params: `offset`, `limit`

---

### `GET` : get song by ID
params: `id`

---

### `GET` : get all genres by song
params: `genres`

---

### `POST` : create new song
body: `title`, `track_number`, `release_id`

headers: `X-Authorization` = `1234`

---

### `PUT` : edit song
params: `id`

body: `title`, `track_number`, `release_id`

headers: `X-Authorization` = `1234`

---

### `DELETE` : delete song
params: `id`

headers: `X-Authorization` = `1234`


<br><br>



# /genres

`http://localhost:8888/php-music/genres`

---


### `GET` : get all genres
params: `offset`, `limit`

---

### `GET` : get genre by ID
params: `id`

---

### `GET` : get all songs by genre
params: `songs`

---

### `POST` : create new genre
body: `name`

headers: `X-Authorization` = `1234`

---

### `PUT` : edit genre
params: `id`

body: `name`

headers: `X-Authorization` = `1234`

---

### `DELETE` : delete genre
params: `id`

headers: `X-Authorization` = `1234`