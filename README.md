## Requirement Analysist
1. User
   * user can have many schedules.
   * user only have one seat in every schedules they have.
   * the data needed for every user is nim / nip, name, email, phone number, and address.
   * every user have default value of credit score a maximum of 15 points.
   * user can pick schedule if credit score >= 10 and the schedule not closed and seat empty.
   * credit score increase 1 point everyday if credit score < 15.
   * user have 1 role from (passenger, co, co leader).
   * user with co or co_leader role can do CRUD for bus identity and bus schedule. can verify user presents in every schedule. can kick / cancel passenger list in every schedule.
   * passenger role can register for him/her self.
   * co role cannot register.
   * co_leader role can register for other co or passenger but cannot register for him/her self but hardcoded instead.
   * co_leader role can do CRUD for other co or passenger.
   * if user have a schedule but not verified until the schedule completed then credit score decrease by 5 points.

2. Schedule
   * schedule only have one bus.
   * schedule only have one route.
   * schedule contains date, route, bus, and closed or not.
   * if schedule date equals to 1 hour before now then schedule automatically deleted.
   * schedule can closed by co or co leader.
   * schedule can have many seats
   * only co, co_leader can create, update, delete schedule
   * everyone can read schedule

3. Route
   * route can refers to many schedule.
   * the data needed from route is route_name.

4. Bus
   * bus can refers many schedules.
   * bus can have many seats.
   * the data needed for the bus is identity, available row seat, available column seat, and available backseat (can null).
   * bus identity must unique
   * only co, co_leader can crud bus

5. Seat
   * seat only refers atleast to one bus.
   * seat only refer one schedule
   * seat only refers max to one user.
   * data needed for seat is row position, column position, and backseat position.
   * if row and column position is filled then backeat position null or in reverse.

6. Role
   * can refers to many users
   * data needed for role is role_name (co_leader, co, passenger).

## Endpoint
method | url | request | response | status | description | protected = 1/0
-------|-----|---------|----------|--------|-------------|----------------
POST | api/buses | { identity, available_row, available_col, available_backseat } | { id, identity, available_row, available_col, available_backseat } | 201 | create bus | 1
DELETE | api/buses/:id | - | - | 204 | delete bus by id | 1
PUT | api/buses/:id | { identity, available_row, available_col, available_backseat } | { id, identity, available_row, available_col, available_backseat } | 200 | update bus by id | 1
GET | api/buses | - | [{ id, identity, available_row, available_col, available_backseat }] | 200 | get all buses | 1
GET | api/buses/:id | - | { id, identity, available_row, available_col, available_backseat } | 200 | get bus by id | 1
POST | api/register | { name, nim_nip, address, phone_number, email, password, password_confirmation, role_id } | - | 201 | register | 0
POST | api/login | { nim_nip, password } | { token } | 200 | login | 0
GET | api/logout | - | - | 204 | logout | 1
POST | api/schedules | { time, bus_id, route_id } | { id, time, bus_identity, route_name, closed } | 201 | create bus | 1
GET | api/schedules | - | [{ id, time, bus_identity, route_name, closed }] | 200 | get all schedules | 1
GET | api/schedules/:id | - | { id, time, bus_identity, route_name, closed } | 200 | get schedule by id | 1
PUT | api/schedules/:id | { time, bus_id, route_id, closed } | { id, time, bus_identity, route_name, closed } | 200 | update schedule by id | 1
DELETE | api/schedules/:id | - | - | 204 | delete schedule by id | 1
GET | api/schedules/route/:id | - | [{ id, time, bus_identity, route_name, closed }] | 200 | get all schedules by route id | 1
POST | api/seats | { seat_id } | { seat_number, user_id } | 200 | user attach seat | 1
GET | api/seats/schedule/:id | - | [{ seat_number, id, user_id, verified }] | 200 | get all seat list | 1
DELETE | api/seats/:id | - | - | 204 | user detach seat | 1
PUT | api/seats/:id | { new_seat_id } | { seat_number, id, user_id, verified } | 200 | user move their seat | 1
GET | api/seats/:id/verify | - | { seat_number, id, user_id, verified } | 200 | co/co_leader verify user | 1
GET | api/users | - | [{ id, name, nim_nip, email }] | 200 | get all users | 1
GET | api/passengers | - | [{ id, name, nim_nip, email }] | 200 | get all passengers | 1
GET | api/co | - | [{ id, name, nim_nip, email }] | 200 | get all cos | 1
GET | api/users/:id | - | [{ id, name, nim_nip, email, phone_number, address, credit_score, role_name }] | 200 | get user by id | 1
POST | api/users | { nim_nip, name, email, address, phone_number, password, password_confirmation } | { id, name, nim_nip, email, phone_number, address, credit_score, role_name } | 201 | co leader create co | 1
PUT | api/users/:id | { nim_nip, name, email, address, phone_number, password, password_confirmation } | { id, name, nim_nip, email, phone_number, address, credit_score, role_name } | 200 | user can update their profile | 1
DELETE | api/users/:id | - | - | 204 | user can delete their profile | 1
GET | api/routes | - | [{ id, route_name }] | 200 | get all routes | 1

## Database Structure

### User
 attributes | description
 -----------|------------
 id | PRIMARY KEY , INT
 nim_nip | NOT NULL, UNIQUE, VARCHAR
 name | NOT NULL, VARCHAR
 email | NOT NULL, VARCHAR
 phone_number | NOT NULL, UNIQUE, VARCHAR
 address | NOT NULL, TEXT
 credit_score | NOT NULL, INT, DEFAULT = 15, MAX = 15
 password | NOT NULL, VARCHAR
 role | NOT NULL, FOREIGN KEY -> roles

### Role
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 role_name | NOT NULL, ENUM (passenger, co, co_leader)

### Schedule
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 time | NOT NULL, DATE
 bus_id | NOT NULL, FOREIGN KEY -> busses
 route_id | NOUT NULL, FOREIGN KEY -> routes
 closed | NOT NULL, BOOLEAN

### Route
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 route_name | NOT NULL, ENUM (sby_gsk, gsk_sby)

### Bus
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 identity | NOT NULL, VARCHAR, UNIQUE
 available_row | NOT NULL, INT
 available_col | NOT NULL, INT
 available_backseat | NOT NULL, DEFAULT = 0

### Seat
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 bus_id | NOT NULL, FOREIGN KEY -> buses
 user_id | NULLABLE, FOREIGN KEY -> users
 schedule_id | NOT NULL, FOREIGN KEY -> schedules
 verified | NOT NULL, BOOLEAN, DEFAULT = false
 seat_number | NOT NULL, VARCHAR

### schedule_user (pivot)
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 schedule_id | NOT NULL, FOREIGN KEY -> schedules
 user_id | NOT NULL, FOREIGN KEY -> users