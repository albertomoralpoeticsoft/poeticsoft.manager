const { 
  useState 
} = wp.element
const {
  Button,
  Popover
} = wp.components

const CalendarWithEvents = ({ events }) => {

  console.log(events)

  const [ currentMonth, setCurrentMonth ] = useState(new Date())
  const [ selectedDay, setSelectedDay ] = useState(null)
  const [ popoverAnchor, setPopoverAnchor ] = useState(null)

  const year = currentMonth.getFullYear()
  const month = currentMonth.getMonth()

  return <>CalenarWithEvents</>

  const generateDays = () => {

    const days = []
    const daysInMonth = new Date(year, month + 1, 0).getDate()

    for (let d = 1; d <= daysInMonth; d++) {
      days.push(new Date(year, month, d))
    }

    return days
  }
  const days = generateDays()

  const getEventsForDay = (day) => {

    const dayStr = day.toISOString().slice(0, 10)
    return events.filter(e => e.date === dayStr)
  }

  const handlePrevMonth = () => {

    setCurrentMonth(new Date(year, month - 1, 1))
    setSelectedDay(null)
    setPopoverAnchor(null)
  }

  const handleNextMonth = () => {

    setCurrentMonth(new Date(year, month + 1, 1))
    setSelectedDay(null)
    setPopoverAnchor(null)
  }

  const handleDayClick = (day, event) => {

    const dayEvents = getEventsForDay(day)
    if (dayEvents.length === 0) return
    setSelectedDay(day)
    setPopoverAnchor(event.currentTarget)
  }

  const closePopover = () => {

    setSelectedDay(null)
    setPopoverAnchor(null)
  }
  
  // (
  //   () => {

  //     const firstWeekDay = (new Date(year, month, 1).getDay() + 6) % 7
      
  //     return Array.from({length: firstWeekDay}).map((_, i) => (
  //       <div 
  //         className="DayEmpty"
  //         key={'empty-' + i}
  //       />
  //     ))
  //   }
  // )()

 
  // const firstWeekDay = (new Date(year, month, 1).getDay() + 6) % 7      
  // const emptyDays = Array
  // .from({length: firstWeekDay}).map((_, i) => (
  //   <div 
  //     className="DayEmpty"
  //     key={'empty-' + i}
  //   />
  // ))

  const emptyDays = []

  return <div className="CalendarWithEvents">

    <div className="Navigation">
      <Button 
        isSecondary 
        onClick={ handlePrevMonth }
      >
        { '<' }
      </Button>
      <div className="CurrentMonth">
        { 
          currentMonth.toLocaleString(
            'default', 
            { 
              month: 'long', 
              year: 'numeric' 
            }
          ) 
        }
      </div>
      <Button 
        isSecondary 
        onClick={ handleNextMonth }
      >      
        { '>' }
      </Button>
    </div>
    <div className="Grid">
      { 
        ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']
        .map(dayName => <div
            className="DayName" 
            key={ dayName }
          >
            { dayName }
          </div>
        )
      }
      {
        emptyDays
      }      
      {
        days.map(day => {

          const dayEvents = getEventsForDay(day)
          const isSelected = selectedDay && 
                             selectedDay.toDateString() === day.toDateString()

          return <div
            className={`
              DayMonth
              ${ isSelected ? 'Selected' : '' }
              ${ dayEvents.length ? 'WithEvents' : '' }
            `}
            key={ day.toISOString() }
            onClick={ (e) => handleDayClick(day, e) }
            title={dayEvents.length ? dayEvents.map(e => e.title).join(', ') : ''}
          >
            {day.getDate()}
          </div>
        })
      }
    </div>
    {
      selectedDay && 
      popoverAnchor && (
      <Popover
        className="CalendarDayDetail"
        position="bottom center"
        onClick={ closePopover }
        anchorRef={{ current: popoverAnchor }}
      >
        <div className="Detail">
          <div className="Title">
            Eventos del { selectedDay.toLocaleDateString() }
          </div>
          <div className="Events">
            {
              getEventsForDay(selectedDay)
              .map(
                (event, i) => <div
                  className="Event" 
                  key={i}
                >
                  <span className="Time">
                    {event.time}
                  </span>
                  { ' - '}
                  <span className="Destination">
                    { event.destination }
                  </span>
                </div>
              )
            }
          </div>
        </div>
      </Popover>
    )}
  </div>
}

export default props => {

  const events = [
    { date: '2025-08-04', time: '14:00', destination: 'Canal' },
    { date: '2025-08-10', time: '09:30', destination: 'Grupo' },
    { date: '2025-08-10', time: '15:00', destination: 'Canal' },
    { date: '2025-08-15', time: '11:00', destination: 'Grupo' },
    { date: '2025-08-15', time: '12:00', destination: 'Grupo' },
    { date: '2025-08-15', time: '13:00', destination: 'Grupo' },
    { date: '2025-08-15', time: '14:00', destination: 'Grupo' },
  ];

	return <div className="RulesCalendar">
    <div className="Tools">
      <Button 
        variant="primary" 
        onClick={ props.closeCheckRules }
      >
        De acuerdo, guardar!
      </Button>
    </div>
  </div>
}