
import immutableUpdate from 'immutable-update'
import moment, { weekdays } from 'moment'
moment.locale('es')
import { 
  times
} from 'lodash'

export default (useReducer, meta, setMeta) => {

  const what = [
    {
      label: 'Contenido de la entrada',
      value: 'content',
      default: true
    },
    {
      label: 'Imagen destacada con excerpt',
      value: 'imageexcerpt'
    }
  ]
  const whatdefault = what.find(w => w.default)

  const weekday = [
    'lunes', 
    'martes', 
    'miércoles', 
    'jueves', 
    'viernes', 
    'sábado', 
    'domingo'
  ]
  const weekdays = times(7, wd => ({
    value: wd,
    label: weekday[wd],
    default: wd == 0
  }))

  const initialsetate = { 
    publishwhat: what,
    publishweekdays: weekdays,
    destinations: [],
    publishnowwhat: whatdefault.value,
    publishnowwhere: [],
    publishing: false,
    message: '',
    publishRules: [],
    selectedRuleIndex: -1,
    checkRulesModalVisible: false
  }

  const updateRules = rules => {

    setMeta({
      ...meta,
      poeticsoft_post_publish_telegram_publishrules: JSON.stringify(rules)
    })
  }
  
  return useReducer(    
    (state, action) => {

      let newState, field, 
      actualRules = state.publishRules

      switch(action.type) {

        case 'PUBLISH_RULES_LOAD':

          const savedPublishRules = action.payload || '[]'
          const rules = JSON.parse(savedPublishRules)
          const selectedRuleIndex = rules.length ? 0: -1

          newState = immutableUpdate(
            state,
            {
              publishRules: rules,
              selectedRuleIndex: selectedRuleIndex
            }
          )

          return newState 

        case 'PUBLISH_RULES_CREATE_RULE':

          const what = state.publishwhat.find(w => w.default)
          const where = state.destinations.find(w=> w.default)
          const from = new Date()
          from.setSeconds(0)
          const to = new Date(from)
          to.setDate(from.getDate() + 1);
          const whenweekday = state.publishweekdays.find(wd=> wd.default)
          const nowhour = '12'
          const nowmin = '0'

          actualRules.push({
            active: true,
            title: 'Título de la regla',
            what: what.value,
            where: [where.value],
            whenfrom: from,
            whento: to,
            whenweekday: [whenweekday.value],
            whenhour: nowhour,
            whenmin: nowmin
          })

          updateRules(actualRules)

          newState = immutableUpdate(
            state,
            {
              publishRules: actualRules,
              selectedRuleIndex: actualRules.length - 1
            }
          )

          return newState

        case 'PUBLISH_RULES_UPDATE_PUBLISHNOWWHAT':
        case 'PUBLISH_RULES_UPDATE_PUBLISHNOWWHERE':
          
          field = action.type.replace('PUBLISH_RULES_UPDATE_', '').toLowerCase()

          newState = immutableUpdate(
            state,
            {
              [field]: action.payload
            },
            [field]
          )

          return newState          

        case 'PUBLISH_RULES_DELETE_RULE':
          
          actualRules.splice(
            state.selectedRuleIndex,
            1
          )  

          updateRules(actualRules)

          newState = immutableUpdate(
            state,
            {
              publishRules: actualRules,
              selectedRuleIndex: -1
            }
          )

          return newState

        case 'PUBLISH_RULES_UPDATE_ACTIVE':
        case 'PUBLISH_RULES_UPDATE_TITLE':
        case 'PUBLISH_RULES_UPDATE_WHAT':
        case 'PUBLISH_RULES_UPDATE_WHERE':
        case 'PUBLISH_RULES_UPDATE_WHENFROM':
        case 'PUBLISH_RULES_UPDATE_WHENTO':
        case 'PUBLISH_RULES_UPDATE_WHENWEEKDAY':
        case 'PUBLISH_RULES_UPDATE_WHENHOUR':
        case 'PUBLISH_RULES_UPDATE_WHENMIN':
          
          field = action.type.replace('PUBLISH_RULES_UPDATE_', '').toLowerCase()
          
          actualRules[state.selectedRuleIndex][field] = action.payload 

          console.log(actualRules)

          updateRules(actualRules)

          newState = immutableUpdate(
            state,
            {
              publishRules: actualRules
            }
          )

          return newState

        default:

          newState = immutableUpdate(
            state,
            action
          )
          
          return newState
      }
    },
    initialsetate
  )
}