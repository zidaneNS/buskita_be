## Requirement Analysist
1. User
   * user can have many schedules.
   * user only have one seat in every schedules they have.
   * the data needed for every user is nim / nip, name, email, phone number, and address.
   * every user have default value of credit score a maximum of 15 points.
   * user can pick schedule if credit score >= 10 and the schedule not closed and seat empty.
   * credit score increase 1 point everyday if credit score < 15.
   * user have 1 role from (passenger, co, co leader).
   * user with co or co_leader role can do CRUD for bus identity and bus schedule. can verify user presents in every schedule. can manipulate pasenger list in every schedule.
   * passenger role can register for him/her self.
   * co role cannot register.
   * co_leader role can register for other co or passenger but cannot register for him/her self but hardcoded instead.
   * co_leader role can do CRUD for other co or passenger.
   * if user have a schedule but not verified until the schedule completed then credit score decrease by 5 points.

2. Schedule
   * schedule only have one bus.
   * schedule only have one route.
   * schedule contains date, route, bus, and closed or not.
   * if schedule date equals to 1 hour from now then schedule automatically deleted.
   * schedule can closed by co or co leader.

3. Route
   * route can refers to many schedule.
   * the data needed from route is route_name.

4. Bus
   * bus can refers many schedules.
   * bus can have many seats.
   * the data needed for the bus is identity, available row seat, available column seat, and available backseat (can null).
   * bus identity must unique

5. Seat
   * seat only refers atleast to one bus.
   * seat only refers max to one user.
   * data needed for seat is row position, column position, and backseat position.
   * if row and column position is filled then backeat position null or in reverse.

6. Role
   * can refers to many users
   * data needed for role is role name.

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
 bus_schedule | NOT NULL, DATE
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
 col_position | NOT NULL, DEFAULT = 0
 row_position | NOT NULL, DEFAULT = 0
 backseat_position | NOT NULL, DEFAULT = 0
 user_id | NULLABLE, FOREIGN KEY -> users
 schedule_id | NOT NULL, FOREIGN KEY -> schedules
 verified | NOT NULL, BOOLEAN, DEFAULT = false

### schedule_user (pivot)
 attributes | description
 -----------|------------
 id | PRIMARY KEY, INT
 schedule_id | NOT NULL, FOREIGN KEY -> schedules
 user_id | NOT NULL, FOREIGN KEY -> users

## STEP
### 1. Authentication
#### Admin :
1. register from start with hardcode
2. can login / logout
3. can regis user and co
4. can delete co

#### Co :
1. only register by admin
2. can login / logout

#### User :
1. can register / login / logout

#### request http
* register : POST api/register
  * request : (name, nim_nip, address, phone_number, email, role, password, password_confirmation)
* login : POST api/login
  * request : (nim_nip, password)
* logout : GET api/logout
  * request : (-)

---

### 2. Bus management
#### Admin :
1. can create/update/delete bus

#### Co :
1. same as admin

#### User :
1. -

#### request http
* create : POST api/buses
  * request : (identity, available_row, available_col, available_backseat)
* update : PUT api/buses/{bus}
  * request : (identity, available_row, available_col, available_backseat)
* delete : DELETE api/buses/{bus}
  * request : (-)
* get all bus : GET api/buses/{bus}
  * request : (-)
* get bus by id : GET api/buses/{bus}
  * request : (-)

#### todo
- [x] co, co leader can create
- [x] bus identity unique
- [x] co, co leader can delete
- [x] co, co leader can update
- [x] bus identity unique when update
- [x] co, co leader can get all bus
- [x] co, co leader can get bus by id
- [x] user can't access

---

### 3. Schedule Management
#### Admin :
1. can create/update/delete schedule

#### Co :
2. same as admin

#### User :
1. -

#### request http
* create : POST api/schedules
  * request : (bus_schedule, bus_id, route_id, closed)
* update : PUT api/schedules/{schedule}
  * request : (bus_schedule, bus_id, route_id, closed)
* delete : DELETE api/schedules/{schedule}
  * request : (-)
* get all schedules : GET api/schedules
  * request : (-)
* get schedules by id : GET api/schedules/{schedule}
  * request : (-)

#### todo
- [x] co, co leader can create schedule.
- [x] co, co leader can update schedule.
- [x] co, co leader can delete schedule.
- [x] user can get all schedules.
- [x] user can get schedules by id.
- [x] passenger can't access create, update, delete.
- [x] schedule deleted 1 hour after the date.

---

### 4. Seat Management
#### Admin :
1. can choose seat
2. can see user's seat list
3. can verify or kick user from it seat
4. can change seat position when other seat is empty

#### Co :
1. same as admin

#### User :
1. same as admin without no. 2 & 3

#### request http
* choose seat : POST api/seats
  * request : (schedule_id, bus_id, row_position, col_position, backseat_position)
* change seat position : PUT api/seats/{seat}
  * request : (schedule_id, row_position, col_position, backseat_position)
* see user's seat list : POST api/seats/schedule
  * request : (schedule_id)
* verify user's seat : PUT api/seats/{seat}/verify
  * request : (-)
* kick user from seat : DELETE api/seats/{seat}
  * request : (schedule_id)

#### todo
- [x] seat created when schedule created.
- [x] user can see seat list.
- [x] number of seats depends on available row, column, and backseat of bus.
- [x] user can pick seat if seat is empty and credit score >= 10 and schedule not closed.
- [x] user cannot pick seat if credit score < 10 or schedule closed or seat not empty.
- [x] user only can pick one seat in one schedule.
- [x] co, co_leader can remove user from their seat.
- [x] user can cancel their seat.
- [x] passenger cannot cancel other passenger's seat
- [x] user can change their seat position.
- [x] co, co_leader can verify user
- [x] passenger cannot verify another passenger

---

### 5. Profile Management
#### Admin :
1. can see all users
2. can see all co
3. can see both user and co each profile
4. can delete co or user
5. can update admin profile

#### Co :
1. can update co profile

#### User :
1. same as admin

#### request http
* see all users : GET api/users
  * request : (-)
* see all co : GET api/users/co
  * request : (-)
* see all passengers : GET api/users/passenger
  * request : (-)
* see user / co profile : GET api/users/{user}
  * request : (-)
* delete user / co : DELETE api/users/{user}
  * request : (-)
* update profile : PUT api/users/{user}
  * request : (name, email, phone_number, address, role)

#### todo todo
- [x] co_leader can see all users.
- [x] co only can see all passengers.
- [x] passenger cannot see all users, co, and other passengers.
- [x] co, co_leader can see user's profile.
- [x] passenger cannot see other passenger's profile
- [ ] user can update their profile.
- [ ] other user cannot update other user's profile.
- [ ] co_leader can delete other users.
- [ ] user credit score decrease 5 points if user have unverified seat and the schedule is complete.
- [ ] user credit score increase 1 points everyday if credit score < 15.
