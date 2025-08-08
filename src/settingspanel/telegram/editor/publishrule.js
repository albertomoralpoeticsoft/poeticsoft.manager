import { 
  times,
  padStart
} from 'lodash'
const { 
  SelectControl, 
  CheckboxControl,
  Button,
  TextControl,
  DateTimePicker,
  Popover 
} = wp.components
const { 
  useState
} = wp.element

export default props => {

  const [ dateFromVisible, setDateFromVisible ] = useState(false)
  const [ dateToVisible, setDateToVisible ] = useState(false)
  const hours = times(24, h => h)
  .map(
    h => ({
      value: h,
      label: padStart(h + '', 2, '0')
    })
  )
  const mins = times(12, m => m * 5)
  .map(
    m => ({
      value: m,
      label: padStart(m + '', 2, '0')
    })
  )

	return <div className="Rule">
    <CheckboxControl
      className="RuleActive"
      label="Activa"
      checked={ props.state.publishRules[props.state.selectedRuleIndex].active }
      onChange={ 
        value => props.localDispatch({
          type: 'PUBLISH_RULES_UPDATE_ACTIVE',
          payload: value
        }) 
      }
    />
    <TextControl
      label="Título"
      placeholder="Título de la regla"
      value={ props.state.publishRules[props.state.selectedRuleIndex].title }
      onChange={ 
        value => props.localDispatch({
          type: 'PUBLISH_RULES_UPDATE_TITLE',
          payload: value
        }) 
      }
    />
    <SelectControl
      label="Que publicar"
      value={ props.state.publishRules[props.state.selectedRuleIndex].what }
      options={ props.state.publishwhat }
      onChange={ 
        value => props.localDispatch({
          type: 'PUBLISH_RULES_UPDATE_WHAT',
          payload: value
        }) 
      }
    />
    <SelectControl
      label="Publicar en"
      value={ props.state.publishRules[props.state.selectedRuleIndex].where }
      options={ props.state.destinations }
      multiple
      onChange={ 
        values => props.localDispatch({
          type: 'PUBLISH_RULES_UPDATE_WHERE',
          payload: values
        }) 
      }
    />
    <div className="DateFrom">
      <div className="Date">
        <label class="components-base-control__label">
          Desde
        </label>
        <Button
          variant="secondary"
          onClick={ () => {
             
            setDateToVisible(false)
            setDateFromVisible(!dateFromVisible)
          }}
        >
          <span className="DateDate">
            El { 
              moment(props.state.publishRules[props.state.selectedRuleIndex].whenfrom)           
              .format('DD[ de ]MMMM[ de ]YYYY')
            }
          </span>
          <span className="DateHour">
            A las { 
              moment(props.state.publishRules[props.state.selectedRuleIndex].whenfrom)           
              .format('HH:mm:ss')
            }
          </span>
        </Button>        
      </div>
      {
        dateFromVisible &&
        <Popover  
          className="PopOverDateFrom"
          position="bottom center"
        >
          <DateTimePicker
            currentDate={ props.state.publishRules[props.state.selectedRuleIndex].whenfrom }
            onChange={ 
              value => props.localDispatch({
                type: 'PUBLISH_RULES_UPDATE_WHENFROM',
                payload: value
              }) 
            }
          />
        </Popover>
      }
    </div>
    <div className="DateTo">
      <div className="Date">
        <label class="components-base-control__label">
          Hasta
        </label>
        <Button
          variant="secondary"
          onClick={ () => {

            setDateFromVisible(false)        
            setDateToVisible(!dateToVisible)          
          }}
        >
          <span className="DateDate">
            El { 
              moment(props.state.publishRules[props.state.selectedRuleIndex].whento)           
              .format('DD[ de ]MMMM[ de ]YYYY')
            }
          </span>
          <span className="DateHour">
            A las { 
              moment(props.state.publishRules[props.state.selectedRuleIndex].whento)           
              .format('HH:mm:ss A')
            }
          </span>
        </Button>        
      </div>
      {
        dateToVisible &&
        <Popover 
          className="PopOverDateTo"
          position="bottom center"
        >
          <DateTimePicker
            currentDate={ props.state.publishRules[props.state.selectedRuleIndex].whento }
            onChange={ 
              value => props.localDispatch({
                type: 'PUBLISH_RULES_UPDATE_WHENTO',
                payload: value
              }) 
            }
          />
        </Popover>
      }
    </div>
    <SelectControl
      className="WeekDay"
      label="Qué días"
      value={ props.state.publishRules[props.state.selectedRuleIndex].whenweekday }
      options={ props.state.publishweekdays }
      multiple
      onChange={ 
        values => props.localDispatch({
          type: 'PUBLISH_RULES_UPDATE_WHENWEEKDAY',
          payload: values
        }) 
      }
    />
    <div className="HourMinute">
      <SelectControl
        className="Hour"
        label="Hora"
        value={ props.state.publishRules[props.state.selectedRuleIndex].whenhour }
        options={ hours}
        onChange={ 
          values => props.localDispatch({
            type: 'PUBLISH_RULES_UPDATE_WHENHOUR',
            payload: values
          }) 
        }
      />
      <SelectControl
        className="Minute"
        label="Min"
        value={ props.state.publishRules[props.state.selectedRuleIndex].whenmin }
        options={ mins }
        onChange={ 
          values => props.localDispatch({
            type: 'PUBLISH_RULES_UPDATE_WHENMIN',
            payload: values
          }) 
        }
      />
    </div>
  </div>
}